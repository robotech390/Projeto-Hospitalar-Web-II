<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

/*#[OA\Info(
    version: "1.0.0",
    title: "Minha API Laravel",
    description: "Documentação da API do meu projeto",
    contact: new OA\Contact(
        name: "Seu Nome",
        email: "seu-email@exemplo.com"
    )
)]
#[OA\Server(
    url: "http://localhost:8000/api",
    description: "Ambiente de Desenvolvimento"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer"
)]
*/

/**
 * @OA\Info(
 * title="Minha API de Convênios",
 * version="1.0.0"
 * )
 */


/**
 * @OA\Info(
 *      title="Minha API Documentation",
 *      version="1.0.0",
 *      description="Documentação interativa da minha API Laravel",
 *      @OA\Contact(
 *          email="seu-email@exemplo.com"
 *      )
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Ambiente de Desenvolvimento"
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