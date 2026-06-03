<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
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