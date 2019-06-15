<?php
namespace me\components;
interface IdentityInterface {
    /**
     * @param int $id Identity Number
     * @return IdentityInterface|null
     */
    public static function findIdentity(int $id);
    /**
     * @return int Identity Number
     */
    public function getId(): int;
}