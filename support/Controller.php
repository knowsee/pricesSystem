<?php
namespace support;

class Controller
{

    protected function dataJson(array $data) {
        return $this->baseJson(200, $data, 1, 'ok');
    }

    protected function listJson(array $list, int $count) {
        return $this->baseJson(200, $list, $count, 'ok');
    }

    protected function messageJson(int $code = 200, string $message = 'ok', ?array $data = []) {
        return $this->baseJson($code, $data, 1, $message);
    }

    private function baseJson(int $code, array $data, int $count, string $message) {
        $base = [
            'code' => $code,
            'data' => $data,
            'count' => $count,
            'msg' => $message
        ];
        return json($base);
    }

}