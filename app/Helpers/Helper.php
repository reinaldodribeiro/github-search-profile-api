<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

class Helper
{

    /**
     * Construtor privado para garantir o Singleton.
     */
    private function __construct()
    {
    }

    /**
     * Compara Data inserida com a data atual.
     * Retorna true para data maior ou igual que a atual, e false para data menor que a atual.
     *
     * @param date $data
     * @return boolean
     */
    public static function comparaDataAtual($data = null)
    {
        if($data == null){
            return $data;
        }

        $data_atual = date('Y-m-d');

        if(strtotime($data) >= strtotime($data_atual)){

            return true;

        }else{

            return false;
        }
    }

    /**
     * Formata o campo com a mascara Telefone.
     *
     * @param string $telefone
     * @return string
     */
    public static function formatTelefone(string $telefone = null)
    {
        if($telefone == null){
            return $telefone;
        }

        $formatedPhone = preg_replace('/[^0-9]/', '', $telefone);
        $matches = [];
        preg_match('/^([0-9]{2})([0-9]{4,5})([0-9]{4})$/', $formatedPhone, $matches);
        if ($matches) {
            return '('.$matches[1].') '.$matches[2].'-'.$matches[3];
        }

        return $telefone;
    }

    /**
     * Retorna os números conforme o valor informado.
     *
     * @param string $value
     * @return mixed
     */
    public static function getOnlyNumber($value)
    {
        $numbers = null;

        if(! empty($value)){
            $numbers = preg_replace("/[^0-9]/", "", $value);
            $numbers = strlen($numbers) == 0 ? null : $numbers;
        }

        return $numbers;
    }

    /**
     * Retorna o valor existente no array ($data) conforme o índice ($index).
     * Obs: Caso o índice não exista o retorno será 'nulo'.
     *
     * @param string $index
     * @param array $data
     * @param mixed $return
     * @return mixed
     */
    public static function getValue($index, $data, $return = null)
    {
        return isset($index) && isset($data) && array_key_exists($index, $data) ? $data[$index] : $return;
    }

    /**
     * Realiza upload de qualquer imagem.
     * Todo Request de imagem, mandar com o nome "imagem"
     *
     * @param $imagem
     * @param string $caminho
     * @return array
     */
    public static function UploadImagem($imagem, string $caminho)
    {

        $img_compact = Uuid::uuid4();
        $extensao = $imagem->getClientOriginalExtension();
        $nomearquivo = $img_compact . "." . $extensao;
        $caminho = public_path($caminho);

        if ($extensao != "jpg" && $extensao != "png" && $extensao != "gif" && $extensao != "bmp" && $extensao != "jpge" && $extensao != "jpeg" && $extensao != "jfif") {

            $error_imagem = [
                "imagem" => [
                    "Formato de arquivo não suportado !"
                ]
            ];

            return array("return" => false, "errors" => $error_imagem);

        } else {

            if ($imagem->move($caminho, $nomearquivo)) {

                return array("return" => true, "imagem_save" => $nomearquivo);

            } else {

                $error_upload = [
                    "imagem" => [
                        "Não foi possível carregar a imagem !"
                    ]
                ];

                return array("return" => false, "errors" => $error_upload);

            }
        }


        /*

         $imagem_size = Image::make($imagem->getRealPath());

        $tamanho = getimagesize($imagem);
        if ($tamanho[0] > 1024 || $tamanho[1] > 1024) {

            $uplaod = $imagem_size->resize(960, 960)->save($caminho . $nomearquivo, 90);

            if (!$uplaod) {
                $error_upload = [
                    "imagem" => [
                        "Não foi possível carregar a imagem !"
                    ]
                ];

                return array("return" => false, "errors" => $error_upload);

            }else{

                return array("return" => true, "imagem_save" => $caminho . $nomearquivo);

            }
        }

        */

    }

}
