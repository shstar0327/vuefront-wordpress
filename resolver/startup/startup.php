<?php

use GraphQL\GraphQL;
use GraphQL\Utils\BuildSchema;

class VFA_ResolverStartupStartup extends VFA_Resolver
{
    public function index()
    {
        if ($this->request->get_method() === 'POST') {
            if ($this->request->get_param('cors')) {
                if (! empty($_SERVER['HTTP_ORIGIN'])) {
                    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
                } else {
                    header('Access-Control-Allow-Origin: *');
                }
                header('Access-Control-Allow-Methods: POST, OPTIONS');
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Allow-Headers: accept,Referer,content-type,x-forwaded-for,DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,cache-control,Content-Type,Range,Token,token,Cookie,cookie,content-type');
            }

            $this->load->model('startup/startup');
            $this->load->model('common/vuefront');

            try {
                $resolvers = $this->model_startup_startup->getResolvers();
                $files = array(VFA_DIR_PLUGIN . 'schema.graphql');

                if ($this->model_common_vuefront->checkAccess()) {
                    $files[] = VFA_DIR_PLUGIN . 'schemaAdmin.graphql';
                }

                $sources = array_map('file_get_contents', $files);

                $source = $this->model_common_vuefront->mergeSchemas(($sources));
                $schema    = BuildSchema::build($source);
                $rawInput  = file_get_contents('php://input');
                $input     = json_decode($rawInput, true);
                $query     = $input['query'];

                $variableValues = isset($input['variables']) ? $input['variables'] : null;
                $result         = GraphQL::executeQuery($schema, $query, $resolvers, null, $variableValues);
            } catch (\Exception $e) {
                $result = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
            }

            $this->response->set_data($result);
            return $this->response;
        } else {
            header('Content-type: text/html');
            echo "<html> <head> <title>Simple GraphiQL Example</title> <link href='https://unpkg.com/graphiql/graphiql.min.css' rel='stylesheet'/> </head> <body style='margin: 0;'> <div id='graphiql' style='height: 100vh;'></div><script crossorigin src='https://unpkg.com/react/umd/react.production.min.js' ></script> <script crossorigin src='https://unpkg.com/react-dom/umd/react-dom.production.min.js' ></script> <script crossorigin src='https://unpkg.com/graphiql/graphiql.min.js' ></script> <script>const fetcher=GraphiQL.createFetcher({url: window.location.href}); ReactDOM.render( React.createElement(GraphiQL,{fetcher: fetcher}), document.getElementById('graphiql'), ); </script> </body> </html>";
            exit;
        }
    }

    public function determine_current_user($user)
    {
        $this->load->model('common/token');

        $validate_uri = strpos($_SERVER['REQUEST_URI'], 'token/validate');
        if ($validate_uri > 0) {
            return $user;
        }

        $token = $this->model_common_token->validateToken(false);

        if (! $token) {
            return $user;
        }

        return $token->data->user->id;
    }
}
