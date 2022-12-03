const express = require('express');
const app = express();
var querystring = require('querystring');
const axios = require('axios');
const url = require('url');
var request = require('request');


const port = 4202;

var client_id = '';
var client_secret = "";
var redirect_uri = '';



app.get('/get',function(req,res){

  res.send('ok');

});

app.get('/callback', function(req, res) {

  var code = req.query.code || null;
  var state = req.query.state || null;

  if (state === null) {
    res.redirect('/#' +
      querystring.stringify({
        error: 'state_mismatch'
      }));
  } else {
    var authOptions = {
      url: 'https://accounts.spotify.com/api/token',
      form: {
        code: code,
        redirect_uri: redirect_uri,
        grant_type: 'authorization_code'
      },
      headers: {
        'Authorization': 'Basic ' + Buffer.from(client_id + ':' + client_secret).toString('base64')
      },
      json: true
    };

    request.post(authOptions, function(error, response, body) {
      if (!error && response.statusCode === 200) {
        var access_token = body.access_token;
        res.send({
          'access_token': access_token
        });
      }
      else res.send('problem : '+response.body);
      console.log(response.body)
    });


  }
});



app.get('/login', function(req, res) {

  var state = makeid(16);
  var scope = 'user-read-playback-state user-modify-playback-state user-read-currently-playing user-follow-modify user-follow-read user-library-modify user-library-read user-read-private user-read-email playlist-read-private playlist-read-collaborative playlist-modify-private playlist-modify-public';

  res.redirect('https://accounts.spotify.com/authorize?' +
    querystring.stringify({
      response_type: 'code',
      client_id: client_id,
      scope: scope,
      redirect_uri: redirect_uri,
      state: state
    }));
});


app.listen(port, () => {
  console.log(`Example app listening on port ${port}`)
})


function makeid(length) {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}
