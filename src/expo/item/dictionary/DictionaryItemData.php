<?php

namespace expo\item\dictionary;

interface DictionaryItemData {

    public function getRuName(): string;

    public function getTexturePath(): string;
}