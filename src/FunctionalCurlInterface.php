<?php

namespace pavelstudio\src;

interface FunctionalCurlInterface
{
    public function SetParam($field, $value);
    public function EnableRedirects();
    public function DisableRedirects();
}