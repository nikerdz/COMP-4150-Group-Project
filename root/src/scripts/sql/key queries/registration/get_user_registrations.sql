SELECT e.event_name, r.rsvp, e.event_date
FROM Registration r
JOIN Event e ON r.event_id = e.event_id
WHERE r.user_id = ?
ORDER by e.event_date DESC;