<?php

return [
    'directory_list' => [
        'src',
    ],
    'exclude_analysis_directory_list' => [
        'vendor/',
    ],
    'suppress_issue_types' => [
        'PhanTemplateTypeNotDeclaredInFunctionParams',
        'PhanTemplateTypeNotUsedInFunctionReturn'
    ],
    'analyze_signature_compatibility' => false,
];
