<?php

namespace App\Controller\CoreControllers;

use App\AppCore\CustomExceptions\EmptyContentTypeException;
use App\AppCore\CustomExceptions\RequestContentTypeException;
use App\AppCore\CustomExceptions\CustomException;
use App\Service\EntitySerializer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class CoreController extends AbstractController
{
    private Serializer $serializer;
    protected ContainerInterface $cntr;
    protected EntitySerializer $entitySerializer;
    protected array $headers;
    protected bool $is_admin = false;
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new XmlEncoder(), new JsonEncoder()]);
        $this->entitySerializer = new EntitySerializer();
        $this->headers = [];
        $this->em = $em;
    }

    /**
     * @throws EmptyContentTypeException
     * @throws RequestContentTypeException
     */
    public function getDataRequest(Request $request): array
    {
        $form_data = $request->getContent();

        if ($request->getContentType() !== 'json') {
            throw new RequestContentTypeException();
        }

        if (empty($form_data)) {
            throw new EmptyContentTypeException();
        }

        return json_decode($form_data, true);
    }

    public function getDataFromFormData(Request $request): array
    {
        $data = $request->request->all();
        $json = null;

        if ($request->request->has('json')) {
            $json = $request->request->get('json');
            unset($data['data']['json']);
        }

        return [
            'data' => $data,
            'files' => $request->files->all(),
            'json'  => $json
        ];
    }

    public function entityToArray(object $entity, bool $withEntityStructure = true): array
    {
        return $this->entitySerializer->entityToArray($entity, $withEntityStructure);
    }

    public function entityToJson(object $entity): string
    {
        return $this->entitySerializer->entityToJson($entity);
    }

    public function jsonToEntity(array $json_entity, string $entity_name): object
    {
        return $this->entitySerializer->jsonToEntity($json_entity, $entity_name);
    }

    /**
     * @throws Exception
     */
    public function entityArrayToJson(array $array): false|string
    {
        return $this->entitySerializer->entityArrayToJson($array);
    }

    /**
     * @throws Exception
     */
    public function collectionEntityToArray(array $array): array
    {
        return $this->entitySerializer->collectionEntityToPrepareArray($array);
    }

    /**
     * @throws Exception
     */
    public function collectionEntityToArraySpecific(array $array, array $specificStructure): array
    {
        return $this->entitySerializer->collectionEntityToArraySpecific($array, $specificStructure);
    }

    public function arrayToJson(array $array): string
    {
        return $this->entitySerializer->arrayToJson($array);
    }

    protected function getEntityParam(string $param_name, object $obj): mixed
    {
        return $this->entitySerializer->getEntityParam($param_name, $obj);
    }

    protected function parseEntityToArray(array $entity_structure, array $data): array
    {
        return $this->entitySerializer->parseEntityToArray($entity_structure, $data);
    }

    protected function duplicateEntity(object $entity_old, object &$entity_new, array $structure): void
    {
        foreach ($structure as $item) {
            $value = $this->getEntityParam($item, $entity_old);
            $this->setEntityParam($item, $value, $entity_new);
        }
    }

    protected function setEntityParam(string $param_name, $value, object &$entity): void
    {
        $method_name = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $param_name)));

        if (method_exists($entity, $method_name)) {
            $entity->$method_name($value);
        }
    }

    protected function jsonDecodeToArray(string $data): array
    {
        return json_decode($data, true);
    }

    protected function generateStreamedResponse($doc, string $filename): StreamedResponse
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $encodedFilename = str_replace('+', ' ', urlencode($filename));

        $contentType = match ($ext) {
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document;charset=utf-8',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8',
            'csv'  => 'text/csv',
            default => 'application/octet-stream',
        };

        $response = new StreamedResponse(function () use ($doc, $ext) {
            ($ext === 'docx') ? $doc->saveAs('php://output') : $doc->save('php://output');
        });

        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Cache-Control', 'maxage=0');
        $response->headers->set('Content-Disposition', "attachment; filename={$encodedFilename}");

        return $response;
    }

    public function success(): JsonResponse
    {
        return $this->json(['success' => true]);
    }

    /**
     * @throws CustomException
     */
    public function error(?string $message = 'Произошла ошибка', ?int $code = 500, Exception|CustomException $e = null): never
    {
        if ($e instanceof CustomException) {
            $code = $e->getCode();
        }

        throw new CustomException($message, $code, $e);
    }
}
