<?php
session_start();

require("../vendor/autoload.php");

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header('Access-Control-Allow-Headers: *'); 

include('../functions/businessLogic.php');
use \OpenApi\Annotations as OA;

include("header.php");



/**
 * @OA\Info(title="File Upload API", version="1.0")
 */

/**
 * @OA\Post(
 *     path="/upload",
 *     summary="Upload a file",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="file",
 *                     type="string",
 *                     format="binary",
 *                     description="File to upload"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="File uploaded successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(property="file_url", type="string", example="https://example.com/uploads/file.png"),
 *             @OA\Property(property="file_extension", type="string", example="png"),
 *             @OA\Property(property="file_size", type="integer", example=1024)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request or file upload failed",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Could not store file to cloud")
 *         )
 *     ),
 *     @OA\Response(
 *         response=503,
 *         description="Please upload a file",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Please upload file")
 *         )
 *     )
 * )
 */
class upload
{


    public function uploadFile()
    {
        if (!empty($_FILES["file"]["name"])) {

            $name = $_FILES["file"]["name"];
            list($txt, $ext) = explode(".", $name);
            $file_name =  time() . "-" . $name;
            $tmp = $_FILES["file"]["tmp_name"];
            $uploaded_at = date("Y-m-d H:i:s");
            if (move_uploaded_file($tmp, "uploads/" . $file_name)) {
                $path = "uploads/" . $file_name;
                $fp = fopen($path, "rb");
                $size = filesize($path);
            }

            $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";


            $url = $protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . "/" . $path;

            if ($url) {
                // set response code - 200 service unavailable
                http_response_code(200);

                // tell the user
                echo json_encode(array("message" => "success", "file_url" => $url, "file_extention" => $ext, "file_size" => $size));
            } else {
               
                // set response code - 503 service unavailable
                http_response_code(400);

                // tell the user
                echo json_encode(array("message" => "Could not store file to cloud"));
            }
            // unlink($path);
        } else {
            // set response code - 503 service unavailable
            http_response_code(400);

            // tell the user
            echo json_encode(array("message" => "Please upload file"));
        }
    }
}
