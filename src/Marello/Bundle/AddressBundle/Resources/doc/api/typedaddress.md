# Marello\Bundle\AddressBundle\Entity\MarelloTypedAddress

## ACTIONS

### get

Retrieve a specific typed address record.

{@inheritdoc}

### get_list

Retrieve a collection of typed address records.

The list of records that will be returned, could be limited by <a href="https://doc.oroinc.com/api/filters">filters</a>.

{@inheritdoc}

### create

Create a new address record.

The created record is returned in the response.

{@inheritdoc}

{@request:json_api}

Example typed address:

`</web_backend_prefix/api/marellotypedaddresses>`

```JSON
{
   "data":{
      "type":"marellotypedaddresses",
      "attributes":{
        "isDefaultBilling": true,
        "isDefaultShipping": true,
        "phone": "012334434",
        "company": "Test Company",
        "label": "my label",
        "street": "Street",
        "street2": "1",
        "city": "City Of Angels",
        "postalCode": "90210",
        "organization": " ",
        "namePrefix": "Mr",
        "firstName": "First",
        "middleName": "von",
        "lastName": "Name",
        "nameSuffix": "Sr"
      }
   }
}
```
{@/request}

### update

Update an existing address record.

The updated record is returned in the response.

{@inheritdoc}

## FIELDS

### firstName
### lastName
### email

#### create

{@inheritdoc}

**The required field**

#### update

{@inheritdoc}

**Please note:**

*This field is **required** and must remain defined.*