## SWAPI

#### The Star Wars API

---

This Laravel application is a coding exercise to create a small wrapper for accessing the swapi. The instructions for
the exercise were as follows:

#### Requirements

- Build a REST API to connect to the [Star Wars API](https://swapi.dev/documentation#intro)
- Include a readme on how to interact with the API
- Include tests

#### Endpoints to build

1. Return a list of the Starships related to Luke Skywalker
2. Return the classification of all species in the 1st episode
3. Return the total population of all planets in the Galaxy

---

### Instructions

The base URL for the endpoints is `https://your.server/api/v1`

The general pattern for the api is `BASE_URL/{entity}/{search_term}/{optional_relation}` where the search term is the
name or title of the entity.

The available relations for each entity vary. For example, a vehicle entity will contain a `pilots` relation,
while a planet entity will contain a `residents` relation, though both are actually referencing a list of `people`
entities. If the optional relation is omitted, the response will contain _only_ the result of the search, and you
will be able to see the available relations in the response.

The swapi offers the following entities

- people
- films
- starships
- vehicles
- species
- planets

However, this is not an exhaustive wrapper. As such, the entity in the URI is limited to

- people
- films
- planets

---

### Examples

To get the data for the endpoint #1 mentioned above, the URI would be `/people/luke/starships`

Endpoints number 2 and 3 are built to be a bit more specific, so while the same general pattern will still apply when
using the
`films` or `planets` entities, there is a specifically formatted response for the following URIs.

Endpoint #2 would be `/films/hope/species`, or if you prefer, `/films/menace/species`

And #3 would be `/planets/population`

---

### License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
