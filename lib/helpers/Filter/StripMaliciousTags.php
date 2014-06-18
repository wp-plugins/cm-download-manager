<?php


require_once 'Interface.php';

class CMDM_Filter_StripMaliciousTags implements CMDM_Filter_Interface{
    public function filter($content) {
        /*         * **Format Code Snippets ******************************* */
        $content = preg_replace_callback("/<pre>([\s\S]+?)<\/pre>/", function($matches)
        {

            $snippet = $matches[1];
            $snippet = htmlentities($snippet);
            $snippet = nl2br($snippet);

            return '<pre class="cmdm_snippet_background">' . $snippet . '</pre>';
        }, $content);
        /*         * ******************************************************* */
        $content = wpautop($content, false);
        /*
         * use wp_kses only if there's no richtext editor (WHY?)
         */
		$allowed_html = array(
            'a'      => array(
                'href'  => array(),
                'title' => array()
            ),
			'del'     => array(),
			'ins'     => array(),
			'code'     => array(),
			'blockquote' => array(),
            'em'     => array(),
            'strong' => array(),
            'b'      => array(),
            'br'     => array(),
			'ul'     => array(),
			'ol'     => array(),
			'li'     => array(),
			'h1'     => array(),
			'h2'     => array(),
			'h3'     => array(),
			'h4'     => array(),
			'h5'     => array(),
			'h6'     => array(),
			'hr'     => array(),
            'pre'    => array(
                'class' => array(),
            ),
            'p'      => array()
        );
		
        $content = wp_kses($content, $allowed_html);

        return trim($content);
    }
}

?>
