<?php

class ResolverCommonLanguage extends Resolver
{
    private $codename = "d_vuefront";

    public function get()
    {
        $languages = array();

        $languages[] = array(
            'name'   => 'English',
            'code'   => 'en-gb',
            'image'  => '',
            'active' => true
        );

        return $languages;
    }

    public function edit($args)
    {
        return $this->get();
    }
}
