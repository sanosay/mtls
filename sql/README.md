## Index creation for the example 
```sql
CREATE TABLE `members` (
   `id` int(10) unsigned not null auto_increment,
   `username` varchar(20) not null,
   `password` char(35) not null,
   `email` varchar(255) not null,
   `date_joined` datetime not null,
   `login_count` int(10) unsigned default 0,
   PRIMARY KEY(`id`)
) ENGINE=InnoDB;
```

*Before the index creation I would ask :*
- How many times are the queries executed against the RDBMS?
- Is there any caching service?
- How big is the table?
- Are there any other column combinations that are used frequently?
- Is the username unique? 

*After those questions I would conclude to the decision.*

Without knowing the above I would suggest : 

a) A single member by username.
By that I assume username is unique. The query will be like 
```sql
SELECT * FROM members WHERE username = 'something'
```
*equation so : HASH. But because we are using InnoDB then BTREE*
```sql
ALTER TABLE `members`
ADD UNIQUE INDEX `UX_memebers_username` (`username`) USING BTREE ;
```

b) All usernames ordered by the date joined ascending.

```sql
SELECT username FROM members ORDER BY date_joined
```
*If we wanted descending order there is no implementation for it*
```sql
 ALTER TABLE `members` ADD INDEX `IX_date_joined` (`date_joined`) USING BTREE
```

c) All ids and usernames ordered by login count descending.

*No implementation for descending indexes*
```sql
 ALTER TABLE `members` ADD INDEX `IX_login_count` (`login_count`) USING BTREE DESC
```
  