create table trades (
  id mediumint(9) unsigned not null auto_increment,
  user_id mediumint(6) unsigned not null,
  currency_from char(3) not null,
  currency_to char(3) not null,
  amount_sell numeric(8,2) not null,
  amount_buy numeric(8,2) not null,
  rate numeric(10,4) not null,
  time_placed datetime not null,
  origin_country char(2) not null,
  created datetime not null
  primary key (id)
);