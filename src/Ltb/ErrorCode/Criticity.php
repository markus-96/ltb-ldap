<?php

/**
 * 
 */
enum Criticity: string {
    case DANGER = "danger";
    case WARNING = "warning";
    case SUCCESS = "success";
    
    function get_fa_class(): string {
        return match($this) {
            Criticity::DANGER => "fa-exclamation-circle",
            Criticity::WARNING => "fa-exclamation-triangle",
            Criticity::SUCCESS => "fa-check-square"
        };
    }
}
