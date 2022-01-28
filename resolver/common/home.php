<?php

class VFA_ResolverCommonHome extends VFA_Resolver
{
    public function get()
    {
        return array(
            'meta' => array(
                'title' => get_option('blogname'),
                'description' => get_option('blogdescription'),
                'keyword' => ''
            )
        );
    }

    public function searchUrl($args)
    {
        $this->load->model('common/seo');

        $result = $this->model_common_seo->searchKeyword($args['url']);

        return $result;
    }

    public function updateApp($args)
    {
        $this->load->model('common/vuefront');
        $this->model_common_vuefront->editApp($args['name'], $args['settings']);

        return $this->model_common_vuefront->getApp($args['name']);
    }

    public function updateSite($args)
    {
        try {
            $tmpFile = download_url("https://vuefront2019.s3.amazonaws.com/sites/".$args['number']."/vuefront-app.tar");
            VFA_vuefront_rmdir(ABSPATH . 'vuefront');
            $phar = new PharData($tmpFile);
            $phar->extractTo(ABSPATH . 'vuefront');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function authProxy($args)
    {
        $this->load->model('common/vuefront');

        if (!is_user_logged_in()) {
            return;
        }
        $app_info = $this->model_common_vuefront->getApp($args['app']);

        $user = wp_get_current_user();

        $url = str_replace(':id', $user->ID, $app_info['authUrl']);
        $result = $this->model_common_vuefront->request($url, [
            'customer_id' => $user->ID,
        ], $app_info['jwt']);

        if (!$result) {
            return '';
        }

        return $result['token'];
    }

    public function version()
    {
        return "1.0.0";
    }
}
