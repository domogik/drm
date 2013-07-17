<?php
// http://www.yiiframework.com/wiki/175/how-to-create-a-rest-api/
class ApiController extends Controller
{
    // Members
 
    /**
     * Default response format
     * either 'json' or 'xml'
     */
    private $format = 'json';
    /**
     * @return array action filters
     */
    public function filters()
    {
            return array();
    }
 
    // Actions
    public function actionList()
    {
        // Get the respective model instance
        switch($_GET['model'])
        {
            case 'repository':
                $models = Repository::model()->findAll();
                break;
            case 'package':
                $models = Package::model()->findAll();
                break;
            default:
                // Model not implemented error
                $this->_sendResponse(501, sprintf(
                    'Error: Mode <b>list</b> is not implemented for model <b>%s</b>',
                    $_GET['model']) );
                exit;
        }
        // Did we get some results?
        if(is_null($models)) {
            // No
            $this->_sendResponse(200, 
                    sprintf('No items where found for model <b>%s</b>', $_GET['model']) );
        } else {
            // Prepare response
            $rows = array();
            foreach($models as $model)
                $rows[] = $model->attributes;
            // Send the response
            $this->_sendResponse(200, CJSON::encode($rows));
        }
    }
    public function actionView()
    {
        // Check if id was submitted via GET     
        switch($_GET['model'])
        {
            // Find respective model    
            case 'repository':
                $model = Repository::model()->findByPk($_GET['id']);
                break;
            case 'package':
                $model = Package::model()->findByAttributes(array('type_id'=>$_GET['type_id'], 'id'=>$_GET['id']));
                break;
            case 'version':
                $model = Version::model()->findByAttributes(array('type_id'=>$_GET['type_id'], 'package_id'=>$_GET['package_id'], 'number'=>$_GET['version'], 'deployed'=>true));
                break;
            default:
                $this->_sendResponse(501, sprintf(
                    'Mode <b>view</b> is not implemented for model <b>%s</b>',
                    $_GET['model']) );
                exit;
        }
        // Did we find the requested model? If not, raise an error
        if(is_null($model))
            $this->_sendResponse(404, 'No Item found with id '.$_GET['id']);
        else
            $row = $model->attributes;
            $this->_sendResponse(200, CJSON::encode($row));
    }
    public function actionCreate()
    {
    }
    public function actionUpdate()
    {
    }
    public function actionDelete()
    {
    }
    
    private function _sendResponse($status = 200, $body = '', $content_type = 'application/json')
    {
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        // and the content type
        header('Content-type: ' . $content_type);
     
        // pages with body are easy
        if($body != '')
        {
            // send the body
            echo $body;
            exit;
        }
        // we need to create the body if none is passed
        else
        {
            // create some body messages
            $message = '';
     
            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch($status)
            {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }
     
            // servers don't always have a signature turned on 
            // (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
     
            // this should be templated in a real-world solution
            $body = '
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
    </head>
    <body>
        <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
        <p>' . $message . '</p>
        <hr />
        <address>' . $signature . '</address>
    </body>
    </html>';
     
            echo $body;
            exit;
        }
    }
    
    private function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }
}
?>