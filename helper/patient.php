<?php

function generatePatientCode(): string
{
    return 'PT-' . date('ymdHis');
}