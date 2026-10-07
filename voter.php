<html>
    <head>
<link rel="stylesheet" href="styles.css">
</head>
<body>
    Voter Registration ::
<form method="POST" action="">
    <h3>Vote</h3>
<?php
include('db.php');
session_start();

  $vid=$_SESSION['vid'];
    echo "<input type='text' id='voteId' placeholder='Voter ID' value='".$vid."'>";
    ?>
    <select id="party" name="party">
      <option disabled selected>Select Party</option>
      <option>BRS</option>
      <option>CONGRESS</option>
      <option>BJP</option>
    </select>
    <button type="submit" name="B1">Cast My Vote </button> 
</form>
<div id="message"></div>
  <script>
    const voters = {}; // Simulates backend storage

    function register() {
      const id = document.getElementById('regId').value;
      const age = parseInt(document.getElementById('regAge').value);

      if (!id || !age) {
        showMessage("Please enter both ID and Age.");
        return;
      }

    if (voters[id]) {
      showMessage("Voter already registered.");
    } else {
      voters[id] = { age: age, hasVoted: false };
      showMessage("Voter registered successfully.");
      document.getElementById('voteCard').style.display = 'block';
      document.getElementById('registerCard').style.display = 'none';
    }
    }

    function fingerprintAuthenticate() {
      if (!window.PublicKeyCredential) {
        showMessage("Web Authentication API not supported on this browser.");
        return;
      }

      // This is a simplified example of fingerprint authentication using WebAuthn
      // In a real application, you would need to communicate with a server to get challenge and verify response

      const publicKey = {
        challenge: new Uint8Array([ // dummy challenge
          0x8C, 0xFA, 0xB1, 0x3B, 0xC7, 0xA9, 0xD4, 0xE3,
          0xF1, 0x2B, 0xA7, 0xC9, 0xD8, 0xE4, 0xF2, 0x3C
        ]).buffer,
        rp: {
          name: "Fingerprint Voting System"
        },
        user: {
          id: new Uint8Array(16),
          name: "user@example.com",
          displayName: "User"
        },
        pubKeyCredParams: [
          { type: "public-key", alg: -7 }
        ],
        timeout: 60000,
        attestation: "direct"
      };

      navigator.credentials.create({ publicKey })
        .then((credential) => {
          showMessage("Fingerprint authentication successful. You can now register.");
        })
        .catch((err) => {
          showMessage("Fingerprint authentication failed or cancelled.");
          console.error(err);
        });
    }

    function fingerprintRegister() {
      if (!window.PublicKeyCredential) {
        showMessage("Web Authentication API not supported on this browser.");
        return;
      }

      navigator.credentials.get({ publicKey: {} })
        .then((assertion) => {
          showMessage("Fingerprint authentication successful. Registering voter...");
          register();
        })
        .catch((err) => {
          showMessage("Fingerprint authentication failed or cancelled.");
          console.error(err);
        });
    }

    function fingerprintVote() {
      if (!window.PublicKeyCredential) {
        showMessage("Web Authentication API not supported on this browser.");
        return;
      }

      navigator.credentials.get({ publicKey: {} })
        .then((assertion) => {
          showMessage("Fingerprint authentication successful. Recording your vote...");
          vote();
        })
        .catch((err) => {
          showMessage("Fingerprint authentication failed or cancelled.");
          console.error(err);
        });
    }

    function vote() {
      const id = document.getElementById('voteId').value;
      const party = document.getElementById('party').value;

      if (!id || !party || party === "Select Party") {
        showMessage("Please enter ID and select a party.");
        return;
      }

      const voter = voters[id];

      if (!voter) {
        showMessage("Voter not registered.");
        return;
      }

      if (voter.age < 18) {
        showMessage("You must be at least 18 to vote.");
        return;
      }

      if (voter.hasVoted) {
        showMessage("You have already voted.");
        return;
      }

      voter.hasVoted = true;
      voter.votedParty = party;
      showMessage(`Vote recorded for ${party}.`);
    }

    function showMessage(msg) {
      document.getElementById('message').textContent = msg;
    }
  </script>

</body>
  </html>
<?php
if(isset($_POST['B1']))
{
  $partyname=$_POST['party'];
  $voterid=$_SESSION['vid'];

  if($partyname<>"")
  {
    echo "Great!.. You are Selected Party is: ".$partyname;
    $q="INSERT INTO votes(voterid,partyname) VALUES('".$voterid."','".$partyname."')";
        $r=mysqli_query($conn,$q);
    echo "Sucessfully Voted!...";
  }
}

?>