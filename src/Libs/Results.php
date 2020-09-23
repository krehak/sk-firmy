<?php

namespace Krehak\SkFirmy\Libs;

class Results {
    private $data = [];

    public function append(array $items): void {
        foreach($items as $id => $data) {
            if($this->idExists($id)) {
                $this->extendsData($id, $data);
            } else {
                $this->addToData($id, $data);
            }
        }
    }

    public function getAll(): array {
        return array_values($this->data);
    }

    private function idExists(string $id): bool {
        return array_key_exists($id, $this->data);
    }

    private function addToData(string $id, array $data): void {
        $this->data[$id] = $data;
    }

    private function extendsData(string $id, array $data): void {
        $newData = $this->data[$id];

        foreach($data as $key => $value) {
            $newData[$key] = $value;
        }

        $this->data[$id] = $newData;
    }
}
