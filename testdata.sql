insert into event(ID, name,refresh,refresh_expire,time_zone,welcome_message,logo) values (UUID(),"hello",60,CURDATE(),"GMT-04:00", "hello world", x'12abcdef');
insert into contact_page_sections(event_ID,sequential_id,header,content) values (1,1,"Hello","world");
insert into contacts(event_ID,sequential_id,name,address,phone) values (1,1,"Josiah the great","home","(000) 000-0000");
insert into themes(theme_name, theme_color) values ('Cedarville Blue', '#003963');
insert into housing(event_ID,sequential_id, host_name, driver) values (1,1, 'Godzilla', 'Baby');
insert into prayer_partners(event_ID,sequential_id) values (1,1);
insert into attendees(event_ID, sequential_ID,house_ID, prayer_group_ID, name) values (1,1,1, 1, 'First Last');
insert into attendees(event_ID, sequential_ID,house_ID, prayer_group_ID, name) values (1,2,null, null, 'Josiah Bills');
insert into notifications(event_ID, title, body, date, refresh) values (1, 'Data in Tables', 'Look! There\'s data! Isn\'t that exciting?', CURDATE(), 30);
insert into notifications(event_ID, title, body, date, refresh) values (1, 'Notification', 'This is another notification.', CURDATE(), 30);
insert into schedule_items(event_ID,sequential_id, date, start_time, length, description, location, category) values (1,1, CURDATE(), 800, 53, 'Crying internally', 'Nowhere', 'Cedarville Blue');
insert into info_page(event_ID,sequential_id, nav, icon) values (1,1, 'All the INFO', '12abcdef');
insert into info_page_sections(info_page_ID,sequential_id, header, content) values (1,1, 'Info! Info! Info!', 'This is information you need.');
insert into info_page_sections(info_page_ID,sequential_id, header, content) values (1,2, 'So Much Info', 'Even more information!');
insert into users(username, password) values ('Person Person', 'letmein');
insert into event_users(user_ID, event_ID) values (1,1);
insert into schedule_items(event_ID,sequential_id, date, start_time, length, description, location, category) values (1,2, CURDATE(), 856, 72, 'Screaming', 'Closet', 'Cedarville Blue');