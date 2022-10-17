<?php

class Html {

    public function renderModel() {
        $template = file_get_contents(plugins_url("/templates/simple.html", __FILE__));
        return $template;
    }
}
