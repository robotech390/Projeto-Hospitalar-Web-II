<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Projeto Hospitalar Web II API",
    description: "Projeto para gerenciamento hospitalar, incluindo convênios, planos e tipos de cobrança. Esse projeto foi desenvolvido utilizando Laravel, React, ",
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