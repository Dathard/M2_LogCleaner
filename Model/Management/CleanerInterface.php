<?php

namespace Dathard\LogCleaner\Model\Management;

interface CleanerInterface
{
    public function run(): CleanerInterface;

    public function allowedToClean(): bool;
}
