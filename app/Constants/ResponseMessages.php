<?php

namespace App\Constants;

class ResponseMessages
{
    const RESPONSE_API_INDEX = "Data Successfully loaded";
    const RESPONSE_API_CREATE = "Data Successfully created";
    const RESPONSE_API_UPDATE = "Data Successfully updated";
    const RESPONSE_API_DELETE = "Data Successfully deleted";
    const RESPONSE_API_FAILED_INDEX = "Data Failed to load";
    const RESPONSE_API_FAILED_CREATE = "Data Failed to saved";
    const RESPONSE_API_FAILED_UPDATE = "Data Failed to updated";
    const RESPONSE_API_FAILED_DELETE = "Data Failed to deleted";
    const RESPONSE_API_ERROR_CREATE = "Data Failed to save with error ";
    const RESPONSE_API_ERROR_UPDATE = "Data Failed to update with error ";

    const RESPONSE_API_DATA_NOT_FOUND = "Data not found";
}
