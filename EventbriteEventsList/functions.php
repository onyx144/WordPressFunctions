<?php
//EVENT SHORT
function custom_eventbrite_shortcode_with_filter() {
    $filter_status = isset($_GET['event_status']) ? sanitize_text_field($_GET['event_status']) : 'all';
    $current_date = date('Y-m-d');

    // Получаем все ивенты
    $events = get_posts([
        'post_type'      => 'eventbrite_events',
        'posts_per_page' => -1,
        'meta_key'       => 'event_start_date',
        'orderby'        => 'meta_value',
        'order'          => 'DESC',
    ]);

    if (empty($events)) {
        return '<p>No events found.</p>';
    }

    ob_start();

    // Форма фильтра
    ?>
    <form method="get" class="eventbrite-events-filter" style="margin-bottom: 20px;">
        <div class="form-first">
		<p class="event_status_select">Filters</p>
        <div class="select-content">	
		<label for="event_status_select">Status</label>
		<select name="event_status" id="event_status_select">
            <option value="all" <?php selected($filter_status, 'all'); ?>>All</option>
            <option value="upcoming" <?php selected($filter_status, 'upcoming'); ?>>Upcoming</option>
            <option value="past" <?php selected($filter_status, 'past'); ?>>Past</option>
        </select>
		</div>
		</div>
        <button class="event_btn" type="submit">Filter
		<svg class="svg-inline--fa fa-long-arrow-alt-right" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"></path></svg>
		</button>
    </form>
    <?php

    $output = '<div class="custom_webinar_list">';

    foreach ($events as $event) {
        $event_id = $event->ID;
        $event_start_date = get_post_meta($event_id, 'event_start_date', true);
        if (!$event_start_date) continue;

        if ($filter_status === 'upcoming' && $event_start_date < $current_date) continue;
        if ($filter_status === 'past' && $event_start_date >= $current_date) continue;

        $event_title = get_the_title($event_id);
        $event_link_website = get_post_meta($event_id, 'iee_event_link', true);
$event_link_source = get_post_meta($event_id, 'iee_source_link', true);

$is_past_event = ($event_start_date < $current_date);
$event_link_new = get_permalink($event_id);
$event_link = $is_past_event ? $event_link_new  : $event_link_website;

/*if (!$event_link) {
    $event_link = get_permalink($event_id);
}*/
        $event_start_hour = get_post_meta($event_id, 'event_start_hour', true);
        $event_start_minute = get_post_meta($event_id, 'event_start_minute', true);
        $event_start_meridian = get_post_meta($event_id, 'event_start_meridian', true);
        $event_venue = get_post_meta($event_id, 'venue_name', true) ?: 'Online Event';
$event_description = $event->post_content;
        $event_image = get_the_post_thumbnail_url($event_id, 'full') ?: 'https://via.placeholder.com/3000';

        $event_month = date('M', strtotime($event_start_date));
        $event_day = date('d', strtotime($event_start_date));

        $output .= '<div class="col-iee-md-6 archive-event post-' . $event_id . ' eventbrite_events">';

// Ссылка обернёт только кнопку Learn More
$output .= '<a href="' . esc_url($event_link) . '" class="iee_event">';

// Картинка
$output .= '<div class="img_placeholder" style="background: url(\'' . esc_url($event_image) . '\') no-repeat center center; background-size: cover; width: 100%; height: 200px;"></div>';

// Название
$output .= '<div class="event_title" style="margin-top: 15px; font-weight: bold; font-size: 18px;">' . esc_html($event_title) . '</div>';

// Описание — укоротим до 10 слов
$words = explode(' ', wp_strip_all_tags($event_description));
$short_description = implode(' ', array_slice($words, 0, 10));
if (count($words) > 10) {
    $short_description .= '...';
}
$output .= '<div class="event_description" style="margin-top: 10px;">' . esc_html($short_description) . '</div>';

// Ссылка Learn More


$output .= '</a>'; // .iee_event
$output .= '</div>'; // .col-iee-md-6
    }

    $output .= '</div>';
    echo $output;

    return ob_get_clean();
}
add_shortcode('eventbrite_events_with_filter', 'custom_eventbrite_shortcode_with_filter');
php?>