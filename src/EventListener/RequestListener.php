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

        /*
         * ElFinder
         */

        // redirect to correct folder
        // Make sure to get all parameters
        // Get parameters CKEditor, CKEditorFuncNum, langCode
        $query_parameters['CKEditor'] =  $request->get('CKEditor');
        $query_parameters['CKEditorFuncNum'] =  $request->get('CKEditorFuncNum');
        $query_parameters['langCode'] =  $request->get('langCode');
        $query_parameters = '?'.http_build_query($query_parameters,'','&');

        if( preg_match_all("/\/elfinder/", $this->newUrl) && !preg_match_all("/\/emails/", $this->oldUrl) ){ // && !in_array('ROLE_ADMIN', $user->getRoles()) ) {
            $pathLocale_root = preg_replace("/\/elfinder.+$/m","/elfinder", $this->newUrl);

            if($user != null) {
                $user_home_folder = str_replace(' ','_',$user->getId() . '_' . $user->getUsername());
            }
            else {
                $user_home_folder = "public";
            }
            
            $filename = __DIR__ . '/../../public/uploads/'.$user_home_folder;

            if(!file_exists($filename)){
                mkdir($filename, 0777);
            }

            // Check for redirect : home folder must be the same as the actual user
            $pathLocale_checker = $pathLocale_root.'/default/'.$user_home_folder;
            // full path url
            $pathLocale = $pathLocale_root.'/default/'.$user_home_folder.$query_parameters;

            # If URL match USER do not redirect
            if($this->newUrl == $pathLocale_checker) {
                return;
            }

            $event->setResponse(new RedirectResponse($pathLocale));
            return;
        }
        // redirect elfinder for common folders ie: specific email folder
        // Find a way to get email id in order to create the folder and the url for a specific email
        elseif( preg_match_all("/\/elfinder/", $this->newUrl) && preg_match_all("/\/emails/", $this->oldUrl) ) {
            $pathLocale_root = preg_replace("/\/elfinder.+$/m","/elfinder", $this->newUrl);

            // Edit to get the actual email
            $id_email = "email_123456";

            $user_home_folder = $id_email;
            $filename = __DIR__ . '/../../public/uploads/'.$user_home_folder;

            if(!file_exists($filename)){
                mkdir($filename, 0777);
            }

            // Check for redirect : home folder must be the same as the actual user
            $pathLocale_checker = $pathLocale_root.'/default/'.$user_home_folder;
            // full path url
            $pathLocale = $pathLocale_root.'/default/'.$user_home_folder.$query_parameters;

            # If URL match USER do not redirect
            if($this->newUrl == $pathLocale_checker) {
                return;
            }

            $event->setResponse(new RedirectResponse($pathLocale));
            return;
        }

        /*
         * I18n
         */

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

    /*
     * This function return the actual language, or set it to en by default
     */
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