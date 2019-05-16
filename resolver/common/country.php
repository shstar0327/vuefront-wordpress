<?php

class ResolverCommonCountry extends Resolver
{
    private $codename = "d_vuefront";

    public function get($args)
    {
        $country_info = WC()->countries->countries[$args['id']];
        return array(
            'id' => $args['id'],
            'name' => $country_info
        );
    }

    public function getList($args)
    {
        $countries = [];

        $results = WC()->countries->countries;
        $country_total = count($results);

        foreach ($results as $key => $value) {
            $countries[] = $this->get(array( 'id' => $key ));
        }

        return array(
            'content'          => $countries,
            'first'            => $args['page'] === 1,
            'last'             => $args['page'] === ceil($country_total / $args['size']),
            'number'           => (int) $args['page'],
            'numberOfElements' => count($countries),
            'size'             => (int) $args['size'],
            'totalPages'       => (int) ceil($country_total / $args['size']),
            'totalElements'    => (int) $country_total,
        );
    }
}