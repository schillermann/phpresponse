<?php

declare(strict_types=1);

namespace PhpResponses;

final class Template implements Text {
    
    private Text $template;
    
    /** @var array<string, Text> */
    private array $variables;

    /**
     * @param array<string, Text> $variables
     */
    public function __construct(Text $template, array $variables) {
        $this->template = $template;
        $this->variables = $variables;
    }

    public function string(): string {
        $result = $this->template->string();
        foreach ($this->variables as $key => $val) {
            $result = str_replace(
                '${' . $key . '}', 
                $val->string(), 
                $result
            );
        }
        return $result;
    }
}
