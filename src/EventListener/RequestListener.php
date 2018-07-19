<?php

// src/EventListener/RequestListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

class RequestListener
{	
	/**
    * @var routeCollection \Symfony\Component\Routing\RouteCollection
    */
    private $routeCollection;
    /**
    * @var urlMatcher \Symfony\Component\Routing\Matcher\UrlMatcher;
    */
    private $urlMatcher;

    /**
    * @var Security
    */
    private $security;

    private $languages;

    private $defaultLanguage;

    private $oldUrl;
    private $newUrl;

    public function __construct(Security $security, $languages, $defaultLanguage)
    {
        $this->security = $security;
        $this->languages = $languages;
        $this->defaultLanguage = $defaultLanguage;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
    	$request = $event->getRequest();
    	$user = $this->security->getUser();
        $this->newUrl  = $request->getPathInfo();
        $this->oldUrl = $request->headers->get('referer');
        
        // redirect all /en* url to /
        if(preg_match_all("/\/en$/", $this->newUrl) || preg_match_all("/\/en\/$/", $this->newUrl)) {
        	$pathLocale = str_replace('/en','/', $this->newUrl);
        	$event->setResponse(new RedirectResponse($pathLocale));
        	return;
        }
        // redirect all /en/* url to /*
        elseif(preg_match_all("/\/en\//", $this->newUrl)) {
        	$pathLocale = str_replace('/en/','/', $this->newUrl);
        	$event->setResponse(new RedirectResponse($pathLocale));
        	return;
        }

        $locale = $this->checkLanguage();

        // If en or null do nothing
        if( $locale === null ) return;
        $request->setLocale($locale);
        $pathLocale = "/".$locale.$this->newUrl;
        
        //We have to catch the ResourceNotFoundException
        $event->setResponse(new RedirectResponse($pathLocale));
        return;

    }

    private function checkLanguage(){ 
        foreach($this->languages as $language){
            if(preg_match_all("/\/$language\//", $this->newUrl) || $language == 'en')
                return null;
            if(preg_match_all("/\/$language\//", $this->oldUrl) )
                return $language;
        }
        return $this->defaultLanguage;
    }
}