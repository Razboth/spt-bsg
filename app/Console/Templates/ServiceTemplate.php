<?php
namespace namespaceName;

use App\Http\Controllers\Controller;
use pathRepository;
use pathModel;
use Exception;
class className extends Controller
{
    private nameRepository $camelCaseNameRepository;
    public function __construct()
    {
        $this->camelCaseNameRepository = new nameRepository(
            new nameModel()
        );
    }
}
