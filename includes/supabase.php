<?php
/**
 * Supabase REST API Wrapper
 */

require_once __DIR__ . '/config.php';

class Supabase {
    private $url;
    private $key;
    
    public function __construct() {
        $this->url = SUPABASE_URL;
        $this->key = SUPABASE_KEY;
    }
    
    /**
     * Get default headers
     */
    private function getHeaders($extraHeaders = []) {
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];
        return array_merge($headers, $extraHeaders);
    }
    
    /**
     * Make a GET request
     */
    public function get($table, $query = '') {
        $endpoint = $this->url . '/rest/v1/' . $table;
        if ($query) {
            $endpoint .= '?' . $query;
        }
        
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $this->getHeaders(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'data' => json_decode($response, true),
            'code' => $httpCode
        ];
    }
    
    /**
     * Make a POST request (Insert)
     */
    public function insert($table, $data) {
        $endpoint = $this->url . '/rest/v1/' . $table;
        
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $this->getHeaders(),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'data' => json_decode($response, true),
            'code' => $httpCode
        ];
    }
    
    /**
     * Make a PATCH request (Update)
     */
    public function update($table, $query, $data) {
        $endpoint = $this->url . '/rest/v1/' . $table . '?' . $query;
        
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $this->getHeaders(),
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'data' => json_decode($response, true),
            'code' => $httpCode
        ];
    }
    
    /**
     * Make a DELETE request
     */
    public function delete($table, $query) {
        $endpoint = $this->url . '/rest/v1/' . $table . '?' . $query;
        
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $this->getHeaders(),
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'data' => json_decode($response, true),
            'code' => $httpCode
        ];
    }
}
