<?php

class Page {
    private $template;

    public function __construct($template) {
        if (!file_exists($template)) {
            die("Template file not found: $template");
        }
        $this->template = file_get_contents($template);
    }

    public function Render($data) {
        $output = $this->template;

        foreach ($data as $key => $value) {
            $output = str_replace("{{" . $key . "}}", htmlspecialchars($value), $output);
        }

        echo $output;
    }
}

?>
