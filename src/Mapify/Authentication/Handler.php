<?php
namespace Mapify\Authentication;

interface Handler
{
    public function execute($payload);
}
