<?php 

function encryptData($data) {
    $cipherCode = 'AES-256-CTR';
    $secret_key = "mAkrKCmfmRkjfvj";
    $option = 0;
    $secret_iv = '4268745230256985';
    
    return openssl_encrypt(
        $data, 
        $cipherCode, 
        $secret_key, 
        $option, 
        $secret_iv
    );
}

function decryptData($encryptedData) {
    $cipherCode = 'AES-256-CTR';
    $secret_key = "mAkrKCmfmRkjfvj";
    $option = 0;
    $secret_iv = '4268745230256985';

    return openssl_decrypt(
        $encryptedData,
        $cipherCode, 
        $secret_key, 
        $option, 
        $secret_iv);
}

?>