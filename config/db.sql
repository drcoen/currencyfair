create table trades (
  id mediumint(9) unsigned not null auto_increment,
  user_id mediumint(6) unsigned not null,
  currency_from char(3) not null,
  currency_to char(3) not null,
  amount_sell numeric(8,2) unsigned not null,
  amount_buy numeric(8,2) unsigned not null,
  rate numeric(10,4) unsigned not null,
  time_placed datetime not null,
  origin_country char(2) not null,
  created datetime not null
  primary key (id)
);

create table vwap (
  date date not null,
  currency_from char(3) not null,
  currency_to char(3) not null,
  vwap numeric(12,4) unsigned not null,
  volume bigint(20) unsigned not null,
  primary key(date, currency_from, currency_to)
);