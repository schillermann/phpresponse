<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use PhpResponse\Text;
use PhpResponse\Request\Body;
use PhpResponse\Request\Header as RequestHeader;
use PhpResponse\Request\Method;
use PhpResponse\Request\Path;
use PhpResponse\Response\StatusLine\Ok;
use PhpResponse\Response\Header\Header as ResponseHeader;
use PhpResponse\Response\Body as ResponseBody;
use PhpResponse\Response\Media\Wire;
use PhpResponse\Text\TemplateVariable;
use PhpResponse\Text\Literal;
use PhpResponse\Text\Fallback;

final class RequestDetailsText implements Text {
    public function string(): string {
        return (new TemplateVariable(
            new TemplateVariable(
                new TemplateVariable(
                    new TemplateVariable(
                        new Literal("<html><body><h1>Your Browser: ${agent}</h1><p>Method: ${method}</p><p>Path: ${path}</p><p>Body: ${body}</p></body></html>"),
                        "agent",
                        new Fallback(
                            new RequestHeader("User-Agent"),
                            new Literal("Unknown")
                        )
                    ),
                    "method",
                    new Method()
                ),
                "path",
                new Path()
            ),
            "body",
            new Body()
        ))->string();
    }
}

(new Ok(
    new ResponseHeader(
        new ResponseBody(
            new RequestDetailsText()
        ),
        "Content-Type", "text/html"
    )
))->media(new Wire());
