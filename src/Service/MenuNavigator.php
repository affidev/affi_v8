<?php

namespace App\Service;


use App\Entity\Posts\Post;
use App\Entity\Websites\Website;


class MenuNavigator
{
	private array $vartwig=[];


    public function templateControl($vartwig,$twigfile, $page, $city): array
    {
        $this->vartwig=$vartwig;
        if($city) $this->vartwig['description']=$this->vartwig['description']." sur ".$city;
        $this->vartwig['entity']=false;
        $this->vartwig['title']=$page;
        $this->vartwig['menu'][]=['route'=>'contact_module_spwb','i'=>'fa fa-comments-o','name'=>'conversations'];
        //  $this->vartwig['menu'][]=['route'=>'show_website','i'=>'fa fa-newspaper-o','name'=>'publications'];
        $this->vartwig['info']=[
            'name'=>'Infos :',
            'i'=>'fa fa-info-circle',
            'title'=>'Vous pouvez laisser un message ou ouvrir une conversation.',
            'soustitle'=>""];
        $this->vartwig['menu2']=[];
        $this->vartwig['titlepage']='affichange';
        $this->vartwig['nav']=[];
        $this->vartwig['info']=[];
        $this->vartwig['arround']=[];
        $this->vartwig['maintwig']=$twigfile;
        return $this->vartwig;
    }

    /**
     * @param $website Website
     * @param $twigfile
     * @param $page
     * @param $typeuser
     * @return array
     */
    public function websiteinfoObj(Website $website, $twigfile, $page, $typeuser): array
    {
            $this->vartwig['arround']=[];
            $this->vartwig['template']=$website->getTemplate();
            $this->vartwig['tagueries']=$website->getTemplate()->getTagueries(); //implode(",", $template->getKeyword());
            $this->vartwig['title']=$website->getNamewebsite();
            $this->vartwig['description']=$website->getTemplate()->getDescription();
            $this->vartwig['author']="AffiChange, https://affichange.com/msg/formcontact/affichange/Dc7zkgFhLpCoH5a2vbiNGUOZ";
            $this->vartwig['page']=$page;
         /*   $this->vartwig['scope']=[
                "@context"=> "https://schema.org",
                'type'=>$website->getTemplate()->getTagueries()[0]->getName(),
                'name'=>$website->getNamewebsite(),
                'description'=>$website->getTemplate()->getDescription(),
                'adress'=>[
                        "@type"=> "PostalAddress",
                      "streetAddress"=>"",
                      "addressLocality"=> $website->getLocality()->getCity(),
                      "addressRegion"=> "",
                      "postalCode"=> $website->getLocality()->getCode(),
                      "addressCountry"=> "FR"
                ],
                "geo"=>[
                    "@type"=> "GeoCoordinates",
                    "latitude"=> $website->getLocality()->getLatloc(),
                    "longitude"=> $website->getLocality()->getLonloc()
                ],
                "url"=> $website->getUrl()??"",
            ];
         */
            $this->vartwig['arround']=[];
            $this->vartwig['maintwig']=$twigfile;
            return $this->vartwig;
    }

    /**
     * @param $website Website
     * @param $twigfile
     * @param $page
     * @param $typeuser
     * @return array
     */
    public function postinfoObj(Post $post, Website $website, $twigfile, $page, $typeuser): array
    {
        $this->vartwig['arround']=[];
        $this->vartwig['template']=$website->getTemplate();
        $this->vartwig['tagueries']=$website->getTemplate()->getTagueries(); //implode(",", $template->getKeyword());
        $this->vartwig['title']=$post->getTitre();
        $this->vartwig['description']=$post->getSubject();
        $this->vartwig['author']=$website->getNamewebsite();
        $this->vartwig['page']=$page;
        /*   $this->vartwig['scope']=[
               "@context"=> "https://schema.org",
               'type'=>$website->getTemplate()->getTagueries()[0]->getName(),
               'name'=>$website->getNamewebsite(),
               'description'=>$website->getTemplate()->getDescription(),
               'adress'=>[
                       "@type"=> "PostalAddress",
                     "streetAddress"=>"",
                     "addressLocality"=> $website->getLocality()->getCity(),
                     "addressRegion"=> "",
                     "postalCode"=> $website->getLocality()->getCode(),
                     "addressCountry"=> "FR"
               ],
               "geo"=>[
                   "@type"=> "GeoCoordinates",
                   "latitude"=> $website->getLocality()->getLatloc(),
                   "longitude"=> $website->getLocality()->getLonloc()
               ],
               "url"=> $website->getUrl()??"",
           ];
        */
        $this->vartwig['arround']=[];
        $this->vartwig['maintwig']=$twigfile;
        return $this->vartwig;
    }

    /**
     * @param $website
     * @param $twigfile
     * @param $page
     * @param $typeuser
     * @return array
     */
    public function websiteinfo($website, $twigfile, $page, $typeuser): array //array
    {

            $this->vartwig['arround']=[];
            $this->vartwig['template']=$website['template'];
            $this->vartwig['tagueries']=$website['template']['tagueries']; //implode(",", $template->getKeyword());
            $this->vartwig['title']=$website['namewebsite'];
            $this->vartwig['description']=$website['template']['description'];
            $this->vartwig['scope']=[
                'type'=>'spaceWeb',
                'name'=>$this->vartwig['title'],
                'description'=>$this->vartwig['description']];
            return  $this->vartwig;

    }


    /**
     * @param $twigfile
     * @param $title
     * @param Website $website
     * @return array
     */
    public function templatingspaceWeb($twigfile, $title, Website $website): array
    {
        $this->vartwig['tabActivities']=[];

        foreach ($website->getListmodules() as $module){
            $this->vartwig['tabActivities'][]=$module->getClassmodule();
        }
        $this->vartwig['maintwig']=$twigfile;
        $this->vartwig['arround']=[];
        $this->vartwig['title']=$title;
        $this->vartwig['titlepage']=$title;
        $this->vartwig['description']="gestion boardsite";
        $this->vartwig['tagueries'][]=["name"=> "backinfo boardsite"];
        return $this->vartwig;
    }

    /**
     * @param $twigfile
     * @param $title
     * @param Website $website
     * @param $nav
     * @return mixed
     */
    public function newtemplatingspaceWeb($twigfile, $title, Website $website,$nav): mixed
    {
        $this->vartwig['tabActivities']=[];
        foreach ($website->getListmodules() as $module){
            $this->vartwig['tabActivities'][]=$module->getClassmodule();
        }
        $this->vartwig['maintwig']=$twigfile;
        $this->vartwig['arround']=[];
        $this->vartwig['title']=$title;
        $this->vartwig['titlepage']=$title;
        $this->vartwig['description']="gestion boardsite";
        $this->vartwig['tagueries'][]=["name"=> "backinfo boardsite"];
        $this->vartwig['m1']=false;
        $this->vartwig['m2']=false;
        $this->vartwig['m3']=false;
        $this->vartwig['m4']=false;
        $this->vartwig['m5']=false;
        $this->vartwig['m6']=false;
        $this->vartwig['m7']=false;
        $this->vartwig['m'.$nav]=true;
        return $this->vartwig;
    }

    /**
     * @param $twigfile
     * @param $title
     * @param Website $website
     * @return mixed
     */
    public function templatingadmin($twigfile, $title, Website $website,$nav): mixed
    {
        $this->vartwig['tabActivities']=[];
        foreach ($website->getListmodules() as $module){
            $this->vartwig['tabActivities'][]=$module->getClassmodule();
        }
        $this->vartwig['maintwig']=$twigfile;
        $this->vartwig['arround']=[];
        $this->vartwig['title']=$title;
        $this->vartwig['titlepage']=$title;
        $this->vartwig['description']="gestion boardsite";
        $this->vartwig['tagueries'][]=["name"=> "backinfo boardsite"];
        $this->vartwig['m1']=false;
        $this->vartwig['m2']=false;
        $this->vartwig['m3']=false;
        $this->vartwig['m4']=false;
        $this->vartwig['m5']=false;
        $this->vartwig['m6']=false;
        $this->vartwig['m7']=false;
        $this->vartwig['m'.$nav]=true;

        switch ($twigfile) {

            case 'openday':
                $this->vartwig['menu2'][]=['route'=>'opendays_edit','i'=>'fa fa-clock-o','name'=>'horaires','class'=>'navselect'];
                $this->vartwig['menu2'][]=['route'=>'spaceweblocalize_init','i'=>'fa fa-compass','name'=>'Adresse','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'spaceweb_mod','i'=>'fa fa-suitcase','name'=>'modules','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'website_edit','i'=>'fa fa-id-card-o','name'=>'infos','class'=>'no'];
                break;

            case 'localizer':
                $this->vartwig['menu2'][]=['route'=>'opendays_edit','i'=>'fa fa-clock-o','name'=>'horaires','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'spaceweblocalize_init','i'=>'fa fa-compass','name'=>'localisation','class'=>'navselect'];
                $this->vartwig['menu2'][]=['route'=>'spaceweb_mod','i'=>'fa fa-suitcase','name'=>'modules','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'website_edit','i'=>'fa fa-id-card-o','name'=>'infos','class'=>'no'];
                break;

            case 'update':
                $this->vartwig['menu2'][]=['route'=>'opendays_edit','i'=>'fa fa-clock-o','name'=>'horaires','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'spaceweblocalize_init','i'=>'fa fa-compass','name'=>'localisation','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'spaceweb_mod','i'=>'fa fa-suitcase','name'=>'modules','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'website_edit','i'=>'fa fa-id-card-o','name'=>'infos','class'=>'navselect'];
                break;

            case 'stateModules':
            case 'modules/comonModule':
                $this->vartwig['menu2'][]=['route'=>'opendays_edit','i'=>'fa fa-clock-o','name'=>'horaires','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'spaceweblocalize_init','i'=>'fa fa-compass','name'=>'localisation','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'spaceweb_mod','i'=>'fa fa-suitcase','name'=>'modules','class'=>'navselect'];
                $this->vartwig['menu2'][]=['route'=>'website_edit','i'=>'fa fa-id-card-o','name'=>'infos','class'=>'no'];
                break;

            default:
                $this->vartwig['menu2'][]=['route'=>'opendays_edit','i'=>'fa fa-clock-o','name'=>'horaires','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'spaceweblocalize_init','i'=>'fa fa-compass','name'=>'localisation','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'spaceweb_mod','i'=>'fa fa-suitcase','name'=>'modules','class'=>'no'];
                $this->vartwig['menu2'][]=['route'=>'website_edit','i'=>'fa fa-id-card-o','name'=>'infos','class'=>'no'];
                break;
        }

        return $this->vartwig;
    }

    /**
     * @param $website Website
     * @param $twigfile
     * @param $title
     * @param $member
     * @return mixed
     */
    public function dispatchinfo(Website $website, $twigfile, $title, $member): mixed //objet
    {
        $this->vartwig['tabActivities']=[];
        foreach ($website->getListmodules() as $module){
            $this->vartwig['tabActivities'][]=$module->getClassmodule();
        }
        $this->vartwig['arround']=[];
        $this->vartwig['template']=$website->getTemplate();
        $this->vartwig['tagueries']=$this->vartwig['template']->getTagueries(); //implode(",", $template->getKeyword());
        $this->vartwig['title']=$title;
        $this->vartwig['titlepage']=$title;
        $this->vartwig['description']=$this->vartwig['template']->getDescription();
        $this->vartwig['scope']=[
            'type'=>'spaceWeb',
            'name'=>$this->vartwig['title'],
            'description'=>$this->vartwig['description']];
        return  $this->vartwig;
    }

    /**
     * @param $vartwig
     * @param $twigfile
     * @param $page
     * @param $nav
     * @return array
     */
    public function newtemplateControlCustomer($vartwig, $twigfile, $page, $nav): array
    {
        $this->vartwig=$vartwig;
        $this->vartwig['entity']=false;
        $this->vartwig['title']=$page;
        $this->vartwig['m1']=false;
        $this->vartwig['m2']=false;
        $this->vartwig['m3']=false;
        $this->vartwig['m4']=false;
        $this->vartwig['m5']=false;
        $this->vartwig['m6']=false;
        $this->vartwig['m7']=false;
        $this->vartwig['m'.$nav]=true;
        $this->vartwig['maintwig']=$twigfile;
        return $this->vartwig;
    }
}
