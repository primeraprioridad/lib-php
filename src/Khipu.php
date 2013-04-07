<?php



/**
 * Definimos la ruta de Khipu.
 */
define('KHIPU_ROOT', dirname(__FILE__) . '/');

/**
 * Provee y centraliza la carga de los servicios que presta Khipu.
 */
class Khipu
{
  /**
   * Version del servicio de Khipu.
   */
  const VERSION_KHIPU_SERVICE = '1.1';
  
  /**
   * Version
   */
  const VERSION = '1.0';
  
  /**
   * Corresponde a la ID del cobrador.
   * 
   * @var string
   */
  protected $receiver_id;
  
  /**
   * Corresponde a la llave del cobrador.
   * 
   * @var string
   */
  protected $secret;
  
  /**
   * Opcionalmente para identificar al cobrador que utilizara Khipu.
   * 
   * Solo se requiere para usar los siguientes servicio:
   *   - CreateEmail
   *   - CreatePaymentPage
   * 
   * @param string $receiver_id
   *   Identificador dado por el servicio Khipu.
   * @param string $secret
   *   La llave secreta del identificador.
   */
  public function authenticate($receiver_id, $secret) {
    $this->receiver_id = $receiver_id; 
    $this->secret = $secret; 
  }
  
  /**
   * Carga el servicio y retorna el objeto, en caso de no existir el servicio,
   * se invoca un excepcion.
   */
  public function loadService($service_name) {
    // Definimos el nombre de la clase completa del servicio.
    $class = 'KhipuService' . $service_name;
    // Asignamos la ruta del archivo que contiene la clase.
    $filename = KHIPU_ROOT . 'KhipuService/' . $class . '.php';
    
    // Consultamos si existe el archivo.
    if (file_exists($filename)) {
      // Si existe se llama.
      require_once $filename;
      // Se consulta por el servicio para realizar la carga correspondiente.
      switch ($service_name) {
        case 'CreateEmail':
        case 'CreatePaymentPage':
          // Es requerido identificarse para usar estos servicios.
          if ($this->receiver_id && $this->secret) {
            return new $class($this->receiver_id, $this->secret);
          }
          // Invocamos un Exception
          throw new Exception("Is necessary to authenticate to use the service \"$service_name\"");
        // VerifyPaymentNotification no requiere receiver_id y secret
        case 'VerifyPaymentNotification':
          return new $class();
      }
    }
    // Si no existe el servicio se invoca un Exception
    throw new Exception("The service \"$service_name\" does not exist");
    
  }
  
  /**
   * Funcion que retorna las URL de los servicios de Khipu.
   * 
   * @param string $service_name
   *   Nombre del servicio
   */
  public static function getUrlService($service_name) {
    $url_khipu = 'https://khipu.com/api/1.1/';
    switch ($service_name) {
      case 'CreateEmail':
        return $url_khipu . 'createEmail';
      case 'CreatePaymentPage':
        return $url_khipu . 'createPaymentPage';
      case 'VerifyPaymentNotification':
        return $url_khipu . 'verifyPaymentNotification';
      default:
        return FALSE;
    }
  }
  
  /**
   * Funcion que retorna la lista de botones que da a disposición Khipu.
   */
  public static function getButtonsKhipu() {
    $url = 'https://s3.amazonaws.com/static.khipu.com';
    return array(
      '50x25'     => $url . '/buttons/50x25.png',
      '100x25'    => $url . '/buttons/100x25.png',
      '100x50'    => $url . '/buttons/100x50.png',
      '150x25'    => $url . '/buttons/150x25.png',
      '150x50'    => $url . '/buttons/150x50.png',
      '150x75'    => $url . '/buttons/150x75.png',
      '150x75-B'  => $url . '/buttons/150x75-B.png',
      '200x50'    => $url . '/buttons/200x50.png',
      '200x75'    => $url . '/buttons/200x75.png',
    );
  }
}
