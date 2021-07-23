<?php

namespace App\Controllers;


use App\Services\AdsGateway;
use PDO;

class AdsController
{

    private $db;
    private $requestMethod;
    private $adsId;
    private $adsGateway;

    /**
     * AdsController constructor.
     * @param PDO $db
     * @param string $requestMethod
     * @param int|null $adsId
     */
    public function __construct(PDO $db, string $requestMethod, ?int $adsId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->adsId = $adsId;
        $this->adsGateway = new AdsGateway($this->db);
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'GET':
                $response = $this->getOne();
                break;
            case 'POST':
                if ($this->adsId) {
                    $response = $this->updateAdsFromRequest($this->adsId);
                } else {
                    $response = $this->createAdsFromRequest();
                };
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    /**
     * @return array
     */
    private function getOne(): array
    {
        $result = $this->adsGateway->getRelevant();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    /**
     * @return array
     */
    private function createAdsFromRequest(): array
    {
        $input = (array)json_decode(file_get_contents('php://input'), true);
        $validation = $this->validateAds($input);
        if ($validation) {
            return $this->unprocessableEntityResponse($validation);
        }
        $result = $this->adsGateway->create($input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'message' => 'OK',
            'code' => 200,
            'data' => $result,
        ]);
        return $response;
    }

    /**
     * @param int $id
     * @return array
     */
    private function updateAdsFromRequest(int $id): array
    {
        $ads = $this->adsGateway->find($id);
        if (!$ads) {
            return $this->notFoundResponse();
        }
        $input = (array)json_decode(file_get_contents('php://input'), true);
        $validation = $this->validateAds($input);
        if ($validation) {
            return $this->unprocessableEntityResponse($validation);
        }
        $result = $this->adsGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    /**
     * @param array|null $input
     * @return string|null
     */
    private function validateAds(?array $input): ?string
    {
        if (!isset($input['price']) || !is_integer($input['price'])) {
            return 'Invalid price';
        }
        if (!isset($input['limits']) || !is_integer($input['limits'])) {
            return 'Invalid limits';
        }
        if (!isset($input['text']) || !is_string($input['text'])) {
            return 'Invalid text';
        }
        if (!isset($input['banner']) || !is_string($input['banner'])) {
            return 'Invalid banner';
        }
        return null;
    }

    /**
     * @param string $messsage
     * @return array
     */
    private function unprocessableEntityResponse(string $messsage): array
    {
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode([
            'message' => $messsage,
            'code' => 400,
            'data' => null,
        ]);
        return $response;
    }

    /**
     * @return array
     */
    private function notFoundResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}