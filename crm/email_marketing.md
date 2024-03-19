##  Mailer - Email Marketing Application

### Developed and maintained by GainHQ


#### Note: All API's link start with the domain name of your application


### Template Builder: 
#### Template builder API and Resource

Request Type: GET 

URL: ```/brands/{brand_id}/templates```  

Result:
```json5
[
    {
        "subject": "your subject",
        "custom_content" : "your custom content here",
        "brand_id" : "1",
        "created_by" : "1",
        "updated_by" : "1",
        "brand": {
            "id": 1,
            "name": "Dell",
            "short_name": "dell",
             //...
        }
    },
    //...
]
```


Request Type: POST
 
URL: ```/brands/{brand_id}/templates```  

Body:
```json5
{
    "subject": "your subject",
    "custom_content" : "your custom content here",
    "brand_id" : 1,
    "created_by" : 1,
    "updated_by" : 1,
}
```

Result: 

```json5
{
    "status": true,
    "message": "Template has been created successfully",
    "template": {
        "subject": "your template",
        "brand_id": 1,
        "updated_at": "2020-03-24T06:48:18.000000Z",
        "created_at": "2020-03-24T06:48:18.000000Z",
        "id": 1
    }
}
```
Request Type: PUT, PATCH
 
URL: ```/brands/{brand_id}/templates/{template}``` 

Body:
```json5
{
    "subject": "your subject update",
    "custom_content" : "your custom content update here",
    "brand_id" : 1,
    "created_by" : 1,
    "updated_by" : 1,
    //...
}
```

Result: 

```json5
{
    "status": true,
    "message": "Template has been updated successfully",
    "template": {
        //...
    }
}
```

Request Type: DELETE
 
URL: ```/brands/{brand_id}/templates/{template}``` 

Result:
```json
{
    "status": true,
    "message": "Template has been deleted successfully"
}
```

### Subscribers: 
#### Subscribers API and Resource

Request Type: GET 

URL: ```/brands/{brand_id}/subscribers```  

Result:
```json5
[
    {
        "id": 1,
        "first_name": "your first name",
        "last_name": "your last name",
        "email": "your@demmo.com",
        "brand_id": 1,
        "status_id": 1,
        "created_by": 1,
    },
    //...
]
```

### Subscribers black listed Api
Request Type: GET 

URL: ```/brands/{brand_id}/subscribers?black_listed=true```  

Result:
```json5
[
    {
        "id": 1,
        "first_name": "your first name",
        "last_name": "your last name",
        "email": "your@demmo.com",
        "brand_id": 1,
        "status_id": 1,
        "created_by": 1,
    },
    //...
]
```


Request Type: POST
 
URL: ```/brands/{brand_id}/subscribers```  

Body:
```json5
{
    "first_name": "your first name",
    "last_name" : "your last name",
    "email" : "Your@demo.com",
    "brand_id" : 1,
    "status_id": "1",
    "created_by": "1",
}
```

Result: 

```json5
{
   "status": true,
    "message": "Subscribers has been created successfully",
    "subscriber": {
        "first_name": "your first name",
        "last_name": "your last name",
        "email": "your@demmo.com",
        "status_id": "1",
        "created_by": "1",
        "brand_id": 1,
        "id": 1
    }
}
```
Request Type: PUT, PATCH
 
URL: ```/brands/{brand_id}/subscribers/{subscriber}``` 

Body:
```json5
{
    "first_name": "your first name ",
    "last_name": "your last name",
    "email": "your@demmo.com",
    "status_id": "1",
    "created_by": "1",
    "brand_id": 1,
    "id": 1
    //...
}
```

Result: 

```json5
{
    "status": true,
    "message": "Subscriber has been updated successfully",
    "subscriber": {
        //...
    }
}
```

Request Type: DELETE
 
URL: ```/brands/{brand_id}/subscribers/{subscriber}``` 

Result:
```json
{
    "status": true,
    "message": "Subscriber has been deleted successfully"
}
```


### Segment API Resources: 

#### List of Segments
Request Type: GET 

URL: ```/brands/{brand_id}/segments```

Result:
```json5
[
    {
      "name": "Segment 2011",
      "segment_logic": "[]",
      "brand_id": 1,
      "status_id": 1,
      "created_by": 1
      //...
    }
]
```


#### Store Segment
Request Type: POST
 
URL: ```/brands/{brand_id}/segments```  

Body:
```json5
{
    "name": "Segment 2011",
    "segment_logic": "[]",
    "brand_id": 1,
    "status_id": 1,
    "created_by": 1
    //...
}
```

Result: 

```json5
{
    "status": true,
    "message": "Segment has been created successfully",
    "segment": {
        "name": "Segment 2011",
        "segment_logic": "[]",
        "brand_id": 1,
        "status_id": 1,
        "created_by": 1
        //...
    }
}
```


#### Update Segment

Request Type: PUT, PATCH
 
URL: ```/brands/{brand_id}/segments/{segment}``` 

Body:
```json5
{
    "name": "Segment 2011",
    "segment_logic": "[]",
    "brand_id": 1,
    "status_id": 1,
    "created_by": 1
    //...
}
```

Result: 

```json5
{
    "status": true,
    "message": "Segment has been updated successfully",
    "segment": {
        //...
    }
}
```

Request Type: DELETE
 
URL: ```/brands/{brand_id}/segments/{segment}``` 

Result:
```json
{
    "status": true,
    "message": "Segment has been deleted successfully"
}
```


### Duplicate Segment API

Request Type: ```POST```
 
URL: ```/brands/{brand_id}/segments/{segment}/duplicate``` 

Result:
```json
{
    "status": true,
    "message": "Segment has been duplicated successfully"
}
```

### Subscriber Blacklist: 
#### Subscribers Add to Blacklist API and Resource


Request Type: POST
 
URL: ```/brands/{brand_id}/add-to-blacklist```  

Body:
```json5
{
  "subscribers": [1, 2, 3]
}
```

Result: 

```json5
{
    "status": true,
    "message": "Subscribers has been blacklisted"
}
```

#### Subscribers Remove from Blacklist API and Resource

Request Type: POST
 
URL: ```/brands/{brand_id}/remove-from-blacklist``` 

Body:
```
{
  "subscribers": [1, 2, 3],
  ...
}
```
Result: 

```json5
{
    "status": true,
    "message": "Subscribers has been remove block list"
}
```


### Subscribers Bulk Import: 

#### Subscribers Preview Imports API and Resource:


Request Type: POST
 
URL: ```/brands/{brand_id}/view-imported```  

Body:
```json5
{
  "subscribers": "subscribers_10000.csv", // comma separated csv file*
}
```

 *csv file field format must be like this schema: 
 
| first_name  | last_name  | email (required) |
|---|---|---|
| Don  | Charles  | charles@mail.me  | 
| John  | Doe  | doe@mail.me  |


Result: 

```json5

{
    "filtered": [
        {
            "first_name": "John Doe",
            "last_name": "",
            "email": "anik@gain.media",
            "errorBag": {
                "email": [
                    "The email already exists or has duplicated values"
                ]
            }
        },
        //...
    ],
    "sanitized": [
        {
            "first_name": "Charles",
            "last_name": "Alice",
            "email": "alice@gain.media",
            //...
        }
    ]

}
```


 **sanitized is list of subscribers without any issue, and filtered is subscribers with issues/missing data on some
  fields.

#### Subscribers Quick Import API and Resource:


Request Type: POST
 
URL: ```/brands/{brand_id}/quick-import```  

Body:
```json5
{
  "subscribers": "subscribers_10000.csv", // comma separated csv file
}
```

```json5
{
    "status": true,
    "message": "21000 Subscribers has been imported successfully"
}

```



#### Subscribers Save Bulk Subscribers API:

Request Type: POST
 
URL: ```/brands/{brand_id}/subscribers/bulk-import``` 

Body:
```json5
{
    "subscribers": [
        {
            "first_name": "Don",
            "last_name": "Charles",
            "email": "charles@gain.media",
            "brand_id": "1",
            "status_id": "1",
            "created_by": "1"
        },
        //...
    ]
}
```
Result: 

```json5
{
    "status": true,
    "message": "10000 Subscribers has been bulk imported successfully."
}
```

### List API and Resources

#### Store List

Request Type: GET 

URL: ```/brands/{brand_id}/lists```

Result:
```json5
[
    {
        "name": "345432 eastern World List",
        "description": "nothing",
        "type": "imported", 
        "status_id": 1,
        "brand_id": 1,
        //...
    },
    //...
]
```


#### Store List
Request Type: POST

URL: ```/brands/{brand_id}/lists```  

Body:
```json5
{
	"name": "Western World List 1211",
	"description": "nothing",
	"type": "imported", // it can be only imported/dynamic
	"status_id": 1,
	"segments": [4], // optional
	"subscribers": [7, 8, 9]  // optional
}
```

Result: 

```json5
{
    "status": true,
    "message": "List has been created successfully",
    "list": {
         "name": "Western List 2011",
         "brand_id": 1,
         "status_id": 1,
         "created_by": 1
         //...
    }
}
```


#### Update List

Request Type: PUT, PATCH

URL: ```/brands/{brand_id}/lists/{list}``` 

Body:
Same as POST Body

Result: 

```json5
{
    "status": true,
    "message": "List has been updated successfully",
    "list": {
     //...
    }
}
```

Request Type: DELETE

URL: ```/brands/{brand_id}/lists/{list}``` 

Result:
```json
{
 "status": true,
 "message": "List has been deleted successfully"
}
```

#### Get Dynamic/Imported Subscribers of List
Request Type: GET

URL: ```/brands/{brand_id}/lists/{list}/subscribers```  

Body:
```json5
[
    {
        "id": 1,
        "first_name": "Esta",
        "last_name": "Satterfield",
        "email": "delia.jast@example.net",
        //...
    },
    {
        "id": 2,
        "first_name": "Franco",
        "last_name": "Hyatt",
        "email": "tadams@example.org",
         //...
    },
    //...
]
```


### Status list API

Request type: GET

url: ```admin/statuses/{type}``` type= 'user/subscriber/campaign' etc


### Campaign API and Resources

#### Store Campaign

Request Type: GET 

URL: `/brands/{brand_id}/campaigns`

Result:
```json5
[
    {
        "name": "New Year Campaign 101",
        "subject": "60% flat off",
        "template_content": "...", 
        "time_period": "...", 
        "start_at": "...", 
        "end_at": "...", 
        "campaign_start_time": "...", 
        "status_id": 1,
        "brand_id": 1,
        "created_by": 1
    },
    //...
]
```


#### Store Campaign
Request Type: POST

URL: ```/brands/{brand_id}/campaigns```  

Body:
```json5
{
    "name": "New Year Campaign 101",
    "subject": "60% flat off",
    "attachments": [
        //fileData: mimes: jpeg, jpg, gif, png, pdf, zip
    ]
}
```

Result: 

```json5
{
    "status": true,
    "message": "Campaign has been created successfully",
    "campaign": {
        "name": "New Year Campaign 101",
        "subject": "...",
        //...
        "attachments": [
            {
                "id": 113,
                "path": "/storage/files/2020/04/campaigns/PrrQcTzuhoGhrHtivTMDSkZ9VumSJ0rjqIRTk0LY.jpeg",
                "type": "attachment",
                //...
            },
        ]
    }

}
```


#### Update Campaign

Request Type: PUT, PATCH

URL: ```/brands/{brand_id}/campaigns/{campaign}``` 

Body:
Same as POST Body

Result: 

```json5
{
    "status": true,
    "message": "Campaign has been updated successfully",
    "campaign": {
     //...
    }
}
```

#### Delete Campaign

Request Type: DELETE

URL: ```/brands/{brand_id}/campaigns/{campaign}``` 

Result:
```json
{
    "status": true,
    "message": "Campaign has been deleted successfully"
}
```

#### Campaign Delivery Settings

Request Type: POST

URL: ```/brands/{brand_id}/campaigns/{campaign}/delivery-settings``` 

Body:
```json5
{
    "time_period": "12:22",
    "start_at": "2020-04-06 17:18:47",
    "end_at": "2020-04-09 17:18:47",
    "campaign_start_time": "14:18:47"
}
```

#### Campaign Audiences API

Request Type: POST

URL: ```/brands/{brand_id}/campaigns/{campaign}/audiences``` 

Body:
```json5
{
    "audiences": {
        "lists": [1,2,3,4,5],
        "subscribers": [1,2,4,5,6,8,9]
    }
}
```
** Lists and subscribers can be null but duplicate values are not allowed.

#### Campaign Template API

Request Type: POST

URL: ```/brands/{brand_id}/campaigns/{campaign}/template``` 

Body:
```json5
{
    "template_content": "Lorem ipsum dolot sit amet..."
}
```

Result: 

```json5
{
    "status": true,
    "message": "Campaign template has been updated successfully",
    "campaign": {
        "id": 60,
        "name": "New Year Campaign 101",
        "subject": "60% flat deal",
        "template_content": "lorem ipsum dolor sit amet...",
        "time_period": "daily",
        "start_at": "2020-04-06T17:18:47.000000Z",
        "end_at": "2020-04-06T17:18:47.000000Z",
        "campaign_start_time": "21:18:47",
        "audiences": [
            {
                "audience_type": "subscriber",
                "audiences": [1,2,3,4,5,6,7,8,9,11],
            }
            //...
        ],
        "attachments": [
            {
                "path": "/storage/files/2020/04/campaigns/nuT9rCuY97fM0gEmKCU2Fay6qTQwP.jpeg",
                "type": "attachment",
            },
            //...
        ],
         //...
    }
}
```

#### Campaign Confirmation API

Request Type: POST

URL: ```/brands/{brand_id}/campaigns/{campaign}/confirm``` 

Body:
```json5
{
    "name": "New Year Campaign 101",
    "subject": "60% flat off",
    "time_period": "monthly",
    "start_at": "2020-04-06 17:18:47",
    "end_at": "2020-04-26 17:18:47",
    "campaign_start_time": "21:18:47",
    "template_content": "lorem...",
    "audiences": {
        "subscribers": [1,2,3,4,5,6,9],
        "lists": [1,2,3,4,5,6,9]
    },
    "attachments": [
        //fileData: mimes: jpeg, jpg, gif, png, pdf, zip
    ]
}
```

Result: 

```json5
{
    "status": true,
    "message": "Campaign has been confirmed successfully",
    "campaign": {
        "id": 60,
        "name": "New Year Campaign 101",
        "subject": "60% flat off",
        //...
    }
}
```

#### Campaign Subscribers API

Request Type: GET

URL: ```/brands/{brand_id}/campaigns/{campaign}/subscribers``` 

Result: 

```json5
[
    {
        "first_name": "Eusebio",
        "last_name": "Hoppe",
        "email": "raynor.lee@example.net",
        //...
        "pivot": {
            "list_id": 1,
            "subscriber_id": 2
        }
    },
]
```

#### Subscribers Count API

Request Type: GET

#### Brand Level API: 
URL: ```/brands/{brand_id}/subscribers-count/{range_type?}``` 

#### App Level API
URL: ```/admin/app/subscribers-count/{range_type?}``` 

*** [#] range_type will only be in '1,2,3,4,5,6,gross', or it can be nullable as well. We made a global range_type for
 all similar date range filtering.
 
 If we use DateRangeHelper@dateRange() these type keys will retrieve following.
 
 | range_type  | Definition  | Schema | Comment |
 |---|---|---|---|
 | 0  | 'Total'  | Year wise | It will give an empty array | 
 | 1  | 'Last 7 Days'  | Weekday wise | It will give an array of today and 7 days back from today | 
 | 2  | 'This Week'  | Weekday wise | first date of week and last date of current week |
 | 3  | 'Last Week'  | Weekday wise | first date of previous week and last date of previous week |
 | 4  | 'This Month'  | Date wise | first date of current month, end date of current month |
 | 5  | 'Last Month'  | Date wise | first date of previous month, end date of previous month |
 | 6  | 'This Year'  | Month wise | first date of current year, end date of current year |
 | gross  | Gross Stats  | Key wise | It will calculate gross count with all the data |

Result: 

```json5
//for range_type = 1,2,3 (Weekday wise)
{
    "Thu": {
        "subscribed": 1,
        "unsubscribed": 1,
        "context": "Thu",
        "date": "2020-04-23"
    },
    //...
}
```
```json5
//for range_type = 4,5 (Date wise)
{
    "2020-03-01": {
        "subscribed": 1,
        "unsubscribed": 0,
        "context": "2020-03-01",
        "date": "2020-03-01"
    },
  //...
}
```

```json5
//for range_type = 6 (Month wise)
{
    "Jan": {
        "subscribed": 12,
        "unsubscribed": 53,
        "context": "Jan",
        "date": "2020-01-31"
    },
  //...
}
```
```json5
//for nothing in range_type  (Year wise)
{
    "2020": {
        "subscribed": 71,
        "unsubscribed": 39
    }
  //...
}
```

```json5
//for range_type = gross
{
    "subscribed": 9987,
    "unsubscribed": 4909
}
```


#### Campaigns Count API

Request Type: GET

#### Brand Level API: 
URL: ```/brands/{brand_id}/campaigns-count/{last24Hours?}``` 

#### App Level API
URL: ```/admin/app/campaigns-count/{last24Hours?}``` 

*** last24Hours should be in 1 or nothing. 1 means last count of last 24 hours

Result: 

```json5
{
    "gross_count": 2,
    "status_wise": {
        "status_sent": 3,
        "status_open": 2,
        "status_clicked": 3,
        "status_rejected": 2,
        "status_new": 5,
        "status_queued": 3,
        "status_bounced": 2,
        "status_soft_bounced": 0,
        "status_de_bounced": 1,
        "status_delivered": 7
    }
}
```




#### Email Statistics Count API

Request Type: GET

#### Brand Level API: 
URL: ```/brands/{brand_id}/email-statistics/{range_type?}``` 

#### App Level API
URL: ```/admin/app/email-statistics/{range_type?}``` 

*** range_type is similar to subscribers count range_type

Result: 

```json5
{
    "Mon": {
        "counts": {
            "status_bounced": {
                "count": 0,
                "rate": 0
            },
            "status_clicked": {
                "count": 3,
                "rate": 6,
                "status": "status_clicked"
            },
            "status_open": {
                "count": 2,
                "rate": 4,
                "status": "status_open"
            },
            "status_delivered": {
                "count": 2,
                "rate": 4,
                "status": "status_delivered"
            },
            "status_sent": {
                "count": 6,
                "rate": 12,
                "status": "status_sent"
            }
        },
        "context": "Mon",
        "date": "2020-04-27"
    },
    //...
}
```

### Settings

#### Delivery settings
Request type: GET

API: ```/brands/{brand_id}/settings/delivery/{provider?}```

Without provider, it will give the default settings 

#### Update delivery settings

Request type: POST',

URL: ```/brands/{brand_id}/settings/delivery```

Body: 
Common and optional 

```json
{
    "form_name": "Anything",
    "from_email": "Something@s.com"
}
```
Only for mailgun
```json5
{
    "provider": "mailgun",
    "domain_name": "https://thisisdomain.com",
    "api_key": "secret",
}
```
Only for amazon ses

```json5
{
    "hostname": "",
    "access_key_id": "",
    "secret_access_key": "",
    "provider": "amazon_ses"
}
```
### Notification events
Request type: GET

URL: ```brands/{brand_id}/notification-events```

Single event: 

Request type: GET

URL: ```brands/{brand_id}/notification-events/{event_id}```

### Notification Template:
Request type: PATCH

URL: ```brands/{brand_id}/notification-templates/{template_id}```
Body: 
```json5
{
    "subject": "Christmas Campaign Has Started",
    "custom_content": "<p>Lorem ipsum dolor sit amet</p>",
    "type": "email", // sms/email/database (Required)
}
```

### Custom Field Builder: 
#### Custom Field builder API and Resource

Request Type: GET 

URL: ```/brands/{brand_id}/settings/custom-fields```  

Result:
```json5
[
  {
    "name": "marked_as",
	"context": "campaign",
	"in_list": 1,
	"is_for_admin": 0
  },
  //...
]
```

Request Type: POST
 
URL: ```/brands/{brand_id}/settings/custom-fields```  

Body:
```json5
{
    "custom_field_type_id":  1,
    "brand_id": 1,  //if any
    "created_by": 1,
    "name": "marked_as",
    "context": "campaign",
    "meta": "",  //optional
    "in_list": 1,
    "is_for_admin": 0 
}
```

Result: 

```json5
{
    "status": true,
    "message": "Custom field has been updated successfully",
    "field": {
        "name": "marked_as",
        "context": "campaign",
        //...
    }
}
```
Request Type: PUT, PATCH
 
URL: ```/brands/{brand_id}/settings/custom-fields/{id}``` 

Body:
```json5
{
  "context": "customers",
  //...
}
```

Result: 

```json5
{
    "status": true,
    "message": "Custom field Type has been updated successfully",
    "field": {
        //...
    }
}
```

Request Type: DELETE
 
URL: ```/brands/{brand_id}/settings/custom-fields/{custom-field}``` 

Result:
```json
{
    "status": true,
    "message": "Custom field has been deleted successfully"
}
```

#### Custom Field builder types API and Resource
Request Type: GET
 
URL: ```/admin/app/custom-field-types``` 

Result:
```json5
[
  {"name":  "textarea"},
  //...
]
```

### Mailgun webhook
URl: `/webhook/mailgun`

#### Campaign Clicked and Open Rate
Request Type: GET
 
URL: ```/brands/{brand_id}/campaigns/{campaign}/email-logs/rates?type=``` 

Query Parameter: 'type' is valid for 'clicked' and 'opened' only
Result:
```text
12.19
```

#### Subscribers add to list API
Request Type: POST
 
URL: ```/brands/{brand_id}/subscribers/add-to-lists``` 

Body:
```json5
{
    "name": "US Youth", // Required if create new list. if it exists then no need to add "lists" key.
    "Subscribers": [1,2,34,5,63],
    "lists": [12,43,2] // Required if attach existing lists. if it exists then no need to add "name" key.
}
```

#### Subscribers remove from list API
Request Type: POST
 
URL: ```/brands/{brand_id}/subscribers/remove-from-lists``` 

Body:
```json5
{
    "Subscribers": [1,2,34,5,63],
    "lists": [12,43,2]
}
```


#### Subscribers Bulk destroy
Request Type: POST
 
URL: ```/brands/{brand_id}/subscribers/bulk-destroy``` 

Body:
```json5
{
    "Subscribers": [1,2,34,5,63]
}
```

Result:
```json5
{
    "status": true,
    "message": "3 Subscribers has been deleted successfully.",
    "error_message": "Failed to delete Subscribers. Dakota Stroman, Josephine Goodwin, Drew O'Reilly"
}
```

#### Subscribers Bulk Status update
Request Type: POST
 
URL: ```/brands/{brand_id}/subscribers/update-status``` 

Body:
```json5
{
    "Subscribers": [1,2,34,5,63],
    "status": "active", //allowed: "active", "unsubscribed", "subscribed" and subscriber status ids
}
```

Result:
```json5
{
    "status": true,
    "message": "Status has been updated successfully"
}
```


