import datetime


def convertdatetime(datetimestring):
    date_time = datetime.datetime.now()
    print(type(date_time))
    print(type(datetime.datetime.strptime(datetimestring,
          '%Y-%m-%dT%H:%M:%S%z')))

    new_date_time = datetime.datetime.strptime(datetimestring,
                                               '%Y-%m-%dT%H:%M:%S%z')

    print("New DateTime1: " + new_date_time.strftime("%a, %d %b %Y %H:%M:%S %z"))
    print("New DateTime2: " + new_date_time.strftime("%a, %d %b %Y %H:%M:%S %Z"))
    print("New DateTime2: " + new_date_time.strftime("%a, %d %b %Y %H:%M:%S"))

    return 0


print(convertdatetime("2020-07-01T10:01:00-05:00"))
