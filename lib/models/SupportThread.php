<?php

/**
 * File contains <description of the class>
 * @package Library
 */

/**
 * <Description for the class>
 *
 * @author SP
 * @version 0.1.0
 * @copyright Copyright (c) 2011, REC
 * @package Library
 */
class CMDM_SupportThread {
    const OPTION_THREAD_NOTIFICATION = 'CMDM_thread_notification';
    const OPTION_THREAD_NOTIFICATION_TITLE = 'CMDM_thread_notification_title';
    const DEFAULT_THREAD_NOTIFICATION = 'Someone has posted a new comment on the topic you subscribed to

Download: [addon_title]
Topic: [thread_title]
Click to see: [comment_link]';
    const DEFAULT_THREAD_NOTIFICATION_TITLE = 'Someone has posted a new comment on the topic you subscribed to';

    const OPTION_NEW_THREAD_NOTIFICATION = 'CMDM_new_thread_notification';
    const OPTION_NEW_THREAD_NOTIFICATION_TITLE = 'CMDM_new_thread_notification_title';
    const DEFAULT_NEW_THREAD_NOTIFICATION = 'Someone has added a new topic to your download support page

Download: [addon_title]
Topic: [thread_title]
Click to see: [comment_link]';
    const DEFAULT_NEW_THREAD_NOTIFICATION_TITLE = 'Someone has added a new topic to your download support page';

    public static function init() {
        add_action('generate_rewrite_rules', array(get_class(), 'fixThreadSlugs'), 999);
        add_filter('query_vars', array(get_class(), 'registerQueryVars'));
        add_filter('CMDM_admin_settings', array(get_class(), 'processNotificationMessageSetting'), 1, 1);
        add_filter('CMDM_admin_settings', array(get_class(), 'processNotificationTitleSetting'), 1, 1);
        add_filter('CMDM_admin_settings', array(get_class(), 'processNewNotificationMessageSetting'), 1, 1);
        add_filter('CMDM_admin_settings', array(get_class(), 'processNewNotificationTitleSetting'), 1, 1);
    }

    public static function getThreadsForDownload($id, $page = 1, $perPage = 10) {
        global $wpdb;
        if ($page == 0)
            $page = 1;
        $offset = ($page - 1) * $perPage;
        $sql = $wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS c.comment_ID FROM {$wpdb->comments} c JOIN {$wpdb->commentmeta} cm ON c.comment_parent=0 AND c.comment_ID=cm.comment_id AND cm.meta_key='_thread_updated' AND c.comment_post_id=%d AND c.comment_approved=1 ORDER BY cm.meta_value*1 DESC LIMIT %d, %d", $id, $offset, $perPage);
        $results = $wpdb->get_col($sql);
        $total = $wpdb->get_var('SELECT FOUND_ROWS()');
        $items = array();
        foreach ($results as $item) {
            $items[] = self::getThread($item);
        }
        return compact('items', 'total');
    }

    public static function renderDaysAgo($date, $gmt = false)
    {
        if (!is_numeric($date)) $date = strtotime($date);
        $current = current_time('timestamp', $gmt);
        $seconds_ago = floor($current - $date);
        if ($seconds_ago < 0) return __('some time ago', 'cm-download-manager');
        else {
            if ($seconds_ago < 60) {
                return sprintf(_n('1 second ago', '%d seconds ago',
                                $seconds_ago, 'cm-download-manager'), $seconds_ago);
            } else {
                $minutes_ago = floor($seconds_ago / 60);
                if ($minutes_ago < 60) {
                    return sprintf(_n('1 minute ago', '%d minutes ago',
                                    $minutes_ago, 'cm-download-manager'),
                            $minutes_ago);
                } else {
                    $hours_ago = floor($minutes_ago / 60);
                    if ($hours_ago < 24) {
                        return sprintf(_n('1 hour ago', '%d hours ago',
                                        $hours_ago, 'cm-download-manager'),
                                $hours_ago);
                    } else {
                        $days_ago = floor($hours_ago / 24);
                        if ($days_ago < 7) {
                            return sprintf(_n('1 day ago', '%d days ago',
                                            $days_ago, 'cm-download-manager'),
                                    $days_ago);
                        } else {
                            $weeks_ago = floor($days_ago / 7);
                            if ($weeks_ago < 4) {
                                return sprintf(_n('1 week ago', '%d weeks ago',
                                                $weeks_ago, 'cm-download-manager'),
                                        $weeks_ago);
                            } else {
                                $months_ago = floor($weeks_ago / 4);
                                if ($months_ago < 12) {
                                    return sprintf(_n('1 month ago',
                                                    '%d months ago',
                                                    $months_ago,
                                                    'cm-download-manager'),
                                            $months_ago);
                                } else {
                                    $years_ago = floor($months_ago / 12);
                                    return sprintf(_n('1 year ago',
                                                    '%d years ago', $years_ago,
                                                    'cm-download-manager'),
                                            $years_ago);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function getThread($thread_id, $withComments = true) {
        $comment = get_comment($thread_id);
        $lastPoster = get_comment_meta($thread_id, '_thread_last_poster', true);
        if (empty($lastPoster))
            $lastPoster = get_comment_author($thread_id);
        $retVal = array(
            'thread_id' => $thread_id,
            'title' => get_comment_meta($thread_id, '_thread_title', true),
            'lastPoster' => $lastPoster,
            'lastUpdate' => get_comment_meta($thread_id, '_thread_updated', true),
            'counter' => get_comment_meta($thread_id, '_thread_posts', true),
            'question' => $comment->comment_content,
            'author' => get_comment_author($thread_id),
            'date' => get_comment_date('Y-m-d H:i', $thread_id),
            'listeners' => get_comment_meta($thread_id, '_thread_listeners', true),
            'resolved' => (bool) get_comment_meta($thread_id, '_thread_resolved', true),
            'post_id' => $comment->comment_post_ID
        );
        $args = array(
            'parent' => $thread_id,
            'status' => 'approve',
            'order' => 'ASC'
        );
        $retVal['comments'] = get_comments($args);
        return $retVal;
    }

    public static function fixThreadSlugs($wp_rewrite) {
        $wp_rewrite->rules = array(
            CMDM_GroupDownloadPage::REWRITE_SLUG . '/(.+?)/topic/add/?$' => $wp_rewrite->index . '?' . CMDM_GroupDownloadPage::POST_TYPE . '=' . $wp_rewrite->preg_index(1) . '&CMDM-comment-action=add',
            CMDM_GroupDownloadPage::REWRITE_SLUG . '/(.+?)/topic/([0-9]{1,})/?$' => $wp_rewrite->index . '?' . CMDM_GroupDownloadPage::POST_TYPE . '=' . $wp_rewrite->preg_index(1) . '&CMDM-comment-action=show&CMDM-comment-id=' . $wp_rewrite->preg_index(2),
            CMDM_GroupDownloadPage::REWRITE_SLUG . '/(.+?)/topic/([0-9]{1,})/add/?$' => $wp_rewrite->index . '?' . CMDM_GroupDownloadPage::POST_TYPE . '=' . $wp_rewrite->preg_index(1) . '&CMDM-comment-action=add&CMDM-parent-id=' . $wp_rewrite->preg_index(2),
            CMDM_GroupDownloadPage::REWRITE_SLUG . '/(.+?)/topic/page/([0-9]{1,})/?$' => $wp_rewrite->index . '?' . CMDM_GroupDownloadPage::POST_TYPE . '=' . $wp_rewrite->preg_index(1) . '&CMDM-comment-action=list&CMDM-comment-page=' . $wp_rewrite->preg_index(2),
                ) + $wp_rewrite->rules;
    }

    public static function registerQueryVars($query_vars) {
        $query_vars = array_merge($query_vars, array(
            'CMDM-comment-action',
            'CMDM-comment-id',
            'CMDM-comment-page',
            'CMDM-parent-id'
                ));
        return $query_vars;
    }

    protected static function _notifyOnFollow($thread, $lastCommentId) {
        $listeners = $thread['listeners'];
        if (!empty($listeners)) {
            $message = get_option(self::OPTION_THREAD_NOTIFICATION, self::DEFAULT_THREAD_NOTIFICATION);
            $title = get_option(self::OPTION_THREAD_NOTIFICATION_TITLE, self::DEFAULT_THREAD_NOTIFICATION_TITLE);
            $post = get_post($thread['post_id']);
            $postTitle = $post->post_title;
            $threadTitle = $thread['title'];
            $commentLink = get_permalink($post->ID) . 'topic/' . $thread['thread_id'] . '#comment-' . $lastCommentId;
            $title = str_replace('[addon_title]', $postTitle, $title);
            $title = str_replace('[thread_title]', $threadTitle, $title);
            $title = str_replace('[comment_link]', $commentLink, $title);
            $message = str_replace('[addon_title]', $postTitle, $message);
            $message = str_replace('[thread_title]', $threadTitle, $message);
            $message = str_replace('[comment_link]', $commentLink, $message);
            foreach ($listeners as $user_id) {
                $user = get_userdata($user_id);
                if (!empty($user->user_email)) {
                    wp_mail($user->user_email, $title, $message);
                }
            }
        }
    }

    protected static function _notifyOnNewThread($thread_id, $post_id) {
        $thread = self::getThread($thread_id, false);
        $post = get_post($post_id);
        $author = $post->post_author;
        if (!empty($author)) {
            $message = get_option(self::OPTION_NEW_THREAD_NOTIFICATION, self::DEFAULT_NEW_THREAD_NOTIFICATION);
            $title = get_option(self::OPTION_NEW_THREAD_NOTIFICATION_TITLE, self::DEFAULT_NEW_THREAD_NOTIFICATION_TITLE);
            $postTitle = $post->post_title;
            $threadTitle = $thread['title'];
            $commentLink = get_permalink($post->ID) . 'topic/' . $thread['thread_id'];
            $title = str_replace('[addon_title]', $postTitle, $title);
            $title = str_replace('[thread_title]', $threadTitle, $title);
            $title = str_replace('[comment_link]', $commentLink, $title);
            $message = str_replace('[addon_title]', $postTitle, $message);
            $message = str_replace('[thread_title]', $threadTitle, $message);
            $message = str_replace('[comment_link]', $commentLink, $message);
            $user = get_userdata($author);
            if (!empty($user->user_email)) {
                wp_mail($user->user_email, $title, $message);
            }
        }
    }

    public static function updateThreadMetadata($thread_id, $lastCommentId, $lastAuthorId, $notify = false, $resolved = false) {
        $thread = self::getThread($thread_id);
        $counter = count($thread['comments']);
        $user = get_userdata($lastAuthorId);
        update_comment_meta($thread_id, '_thread_updated', current_time('timestamp', true));
        update_comment_meta($thread_id, '_thread_posts', $counter);
        if ($resolved)
            update_comment_meta($thread_id, '_thread_resolved', $resolved);
        update_comment_meta($thread_id, '_thread_last_poster', $user->display_name);
        self::_notifyOnFollow($thread, $lastCommentId);
        if ($notify) {
            $listeners = (array) get_comment_meta($thread_id, '_thread_listeners', true);
            $listeners[] = $lastAuthorId;
            $listeners = array_unique($listeners);
            update_comment_meta($thread_id, '_thread_listeners', $listeners);
        }
    }

    public static function getCommentData($comment_id) {
        $comment = get_comment($comment_id);
        $retVal = array(
            'id' => $comment_id,
            'content' => $comment->comment_content,
            'author' => get_comment_author($comment_id),
            'date' => get_comment_date('Y-m-d H:i', $comment_id),
            'daysAgo' => self::renderDaysAgo(get_comment_date('G', $comment_id), true)
        );
        return $retVal;
    }

    public static function addCommentToThread($post_id, $thread_id, $content, $author_id, $notify = false, $resolved = false) {
        $user = get_userdata($author_id);
        $content = trim(wp_kses($content, array(
                    'a' => array(
                        'href' => array(),
                        'title' => array()
                    ),
                    'em' => array(),
                    'strong' => array(),
                    'b' => array(),
                    'pre' => array()
                )));
        if (empty($content))
            $errors[] = __('Content cannot be empty', 'cm-download-manager');
        if (!empty($errors)) {
            throw new Exception(serialize($errors));
        }
        $data = array(
            'comment_post_ID' => $post_id,
            'comment_author' => $user->display_name,
            'comment_author_email' => $user->user_email,
            'user_id' => $author_id,
            'comment_parent' => $thread_id,
            'comment_content' => apply_filters('comment_text', $content),
            'comment_approved' => 1,
            'comment_date' => current_time('mysql')
        );
        $comment_id = wp_insert_comment($data);
        self::updateThreadMetadata($thread_id, $comment_id, $author_id, $notify, $resolved);
        return $comment_id;
    }

    public static function addThread($id, $title, $content, $author_id, $notify = false) {
        $user = get_userdata($author_id);
        $title = trim(wp_kses($title, array()));
        $content = trim(wp_kses($content, array(
                    'a' => array(
                        'href' => array(),
                        'title' => array()
                    ),
                    'em' => array(),
                    'strong' => array(),
                    'b' => array(),
                    'pre' => array()
                )));
        if (empty($title))
            $errors[] = __('Title cannot be empty', 'cm-download-manager');
        if (empty($content))
            $errors[] = __('Content cannot be empty', 'cm-download-manager');
        if (!empty($errors)) {
            throw new Exception(serialize($errors));
        }
        $data = array(
            'comment_post_ID' => $id,
            'comment_author' => $user->display_name,
            'comment_author_email' => $user->user_email,
            'user_id' => $author_id,
            'comment_parent' => 0,
            'comment_content' => apply_filters('comment_text', $content),
            'comment_approved' => 1,
            'comment_date' => current_time('mysql')
        );
        $comment_id = wp_insert_comment($data);
        if ($comment_id) {
            update_comment_meta($comment_id, '_thread_updated', current_time('timestamp'));
            update_comment_meta($comment_id, '_thread_posts', 1);
            update_comment_meta($comment_id, '_thread_title', $title);
            update_comment_meta($comment_id, '_thread_last_poster', $user->display_name);
            if ($notify) {
                $listeners = (array) get_comment_meta($comment_id, '_thread_listeners', true);
                $listeners[] = $author_id;
                $listeners = array_unique($listeners);
                update_comment_meta($comment_id, '_thread_listeners', $listeners);
            }
            self::_notifyOnNewThread($comment_id, $id);
        }
        return $comment_id;
    }

    public static function processNotificationTitleSetting($params = array()) {
        if (!empty($_POST[self::OPTION_THREAD_NOTIFICATION_TITLE])) {
            update_option(self::OPTION_THREAD_NOTIFICATION_TITLE, $_POST[self::OPTION_THREAD_NOTIFICATION_TITLE]);
        }
        $params['threadNotificationTitle'] = get_option(self::OPTION_THREAD_NOTIFICATION_TITLE, self::DEFAULT_THREAD_NOTIFICATION_TITLE);
        return $params;
    }

    public static function processNotificationMessageSetting($params = array()) {
        if (!empty($_POST[self::OPTION_THREAD_NOTIFICATION])) {
            update_option(self::OPTION_THREAD_NOTIFICATION, $_POST[self::OPTION_THREAD_NOTIFICATION]);
        }
        $params['threadNotification'] = get_option(self::OPTION_THREAD_NOTIFICATION, self::DEFAULT_THREAD_NOTIFICATION);
        return $params;
    }

    public static function processNewNotificationTitleSetting($params = array()) {
        if (!empty($_POST[self::OPTION_NEW_THREAD_NOTIFICATION_TITLE])) {
            update_option(self::OPTION_NEW_THREAD_NOTIFICATION_TITLE, $_POST[self::OPTION_NEW_THREAD_NOTIFICATION_TITLE]);
        }
        $params['threadNewNotificationTitle'] = get_option(self::OPTION_NEW_THREAD_NOTIFICATION_TITLE, self::DEFAULT_NEW_THREAD_NOTIFICATION_TITLE);
        return $params;
    }

    public static function processNewNotificationMessageSetting($params = array()) {
        if (!empty($_POST[self::OPTION_NEW_THREAD_NOTIFICATION])) {
            update_option(self::OPTION_NEW_THREAD_NOTIFICATION, $_POST[self::OPTION_NEW_THREAD_NOTIFICATION]);
        }
        $params['threadNewNotification'] = get_option(self::OPTION_NEW_THREAD_NOTIFICATION, self::DEFAULT_NEW_THREAD_NOTIFICATION);
        return $params;
    }

}

?>
