<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Documentação da API",
 * description="Interface de documentação do projeto (Laravel + JWT)",
 * @OA\Contact(
 * email="seu-email@dominio.com"
 * )
 * )
 * * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Servidor de Desenvolvimento Local"
 * )
 * * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * in="header",
 * name="Authorization",
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT",
 * )
 */
abstract class Controller
{
    // Este arquivo pode ficar vazio ou abstrato. 
    // O L5-Swagger vai ler as anotações acima perfeitamente.
}
?>
<?php

/*namespace App\Http\Controllers;

abstract class Controller
{
    //
}
*/