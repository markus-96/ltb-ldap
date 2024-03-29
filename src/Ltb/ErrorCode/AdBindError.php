<?php

/**
 * 
 */
enum AdBindError: int {
    case USER_NOT_FOUND = hexdec("532");
    case INVALID_CREDENTIALS = hexdec("52e");
    case NOT_PERMITTED_LOGON_AT_THIS_TIME = hexdec("530");
    case PASSWORD_EXPIRED = hexdec("532");
    case ACCOUNT_DISABLED = hexdec("533");
    case ACCOUNT_EXPIRED = hexdec("701");
    case USER_MUST_RESET_PASSWORD = hexdec("773");
    case ACCOUNT_LOCKED = hexdec("775");
}
