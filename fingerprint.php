<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Voter - Fingerprint Voting System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #333;
        }
        header {
            background: linear-gradient(90deg, #34495e, #2c3e50);
            color: #ecf0f1;
            width: 100%;
            padding: 1.5rem 0;
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            box-shadow: 0 6px 12px rgba(0,0,0,0.4);
            letter-spacing: 3px;
            text-transform: uppercase;
            transition: background 0.3s ease;
        }
        header:hover {
            background: linear-gradient(90deg, #2c3e50, #34495e);
        }
        nav {
            background: linear-gradient(90deg, #34495e, #2c3e50);
            padding: 0.75rem 0;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: background 0.3s ease;
        }
        nav a {
            color: white;
            margin: 0 1.2rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }
        nav a:hover {
            color: #74ebd5;
        }
        main {
            margin-top: 3rem;
            background: white;
            padding: 3rem 3.5rem;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            width: 360px;
            max-width: 95%;
            text-align: center;
            transition: box-shadow 0.3s ease;
        }
        main:hover {
            box-shadow: 0 16px 40px rgba(0,0,0,0.3);
        }
        .fingerprint-scanner {
            margin: 2rem auto;
            width: 160px;
            height: 160px;
            border: 5px solid #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            user-select: none;
            position: relative;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.4);
        }
        .fingerprint-scanner:hover {
            border-color: #2980b9;
            box-shadow: 0 6px 20px rgba(41, 128, 185, 0.6);
        }
        .fingerprint-icon {
            width: 90px;
            height: 90px;
            fill: #3498db;
            transition: fill 0.3s ease;
        }
        .fingerprint-scanner:hover .fingerprint-icon {
            fill: #2980b9;
        }
        .status {
            font-size: 1.2rem;
            margin-top: 1.5rem;
            min-height: 1.5rem;
            color: #555;
            font-weight: 500;
        }
        button.vote-btn {
            margin-top: 2rem;
            padding: 0.8rem 1.5rem;
            font-size: 1.1rem;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: not-allowed;
            opacity: 0.6;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.4);
            width: 100%;
        }
        button.vote-btn.enabled {
            cursor: pointer;
            opacity: 1;
        }
        button.vote-btn.enabled:hover {
            background-color: #219150;
            box-shadow: 0 6px 18px rgba(33, 145, 80, 0.7);
        }
    </style>
</head>
<body>
    <header>Voter - Fingerprint Voting System</header>
    <nav>
        <a href="index.html">Home</a>
        <a href="voter.html">Voter</a>
        <a href="election_official.html">Election Official</a>
    </nav>
    <main>
        <p>Click the fingerprint scanner below to simulate fingerprint authentication.</p>
        <div class="fingerprint-scanner" id="scanner" title="Click to scan fingerprint">
            <svg class="fingerprint-icon" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                <path d="M32 2C18 2 6 14 6 28c0 10 6 18 14 22v6h4v-6c2 0 4-2 4-4v-4c0-2-2-4-4-4s-4 2-4 4v4h-4v-4c0-6 6-10 10-10s10 4 10 10v4c0 4-4 8-8 8v6h4v-6c8-4 14-12 14-22 0-14-12-26-26-26z"/>
            </svg>
        </div>
        <div class="status" id="status">Waiting for fingerprint scan...</div>
     
        <button class="vote-btn" id="voteBtn" disabled><a href='voter.php'>Cast Vote</a></button>

        <button class="vote-btn" id="voteBtn" disabled>Cast Vote</button>

    </main>

    <script>
        const scanner = document.getElementById('scanner');
        const status = document.getElementById('status');
        const voteBtn = document.getElementById('voteBtn');

        let authenticated = false;

        scanner.addEventListener('click', () => {
            if (authenticated) {
                status.textContent = 'Fingerprint already authenticated. You can cast your vote.';
                return;
            }
            status.textContent = 'Scanning fingerprint...';
            scanner.style.borderColor = '#f39c12';

            // Simulate fingerprint scanning delay
            setTimeout(() => {
                authenticated = true;
                status.textContent = 'Fingerprint authenticated successfully!';
                scanner.style.borderColor = '#27ae60';
                voteBtn.disabled = false;
                voteBtn.classList.add('enabled');
            }, 2000);
        });

        voteBtn.addEventListener('click', () => {
            if (!authenticated) {
                status.textContent = 'Please authenticate your fingerprint first.';
                return;
            }
            status.textContent = 'Vote cast successfully. Thank you for voting!';
            voteBtn.disabled = true;
            voteBtn.classList.remove('enabled');
            scanner.style.borderColor = '#2c3e50';
        });

        // Optional: Handle form submission
        const registrationForm = document.getElementById('registrationForm');
        registrationForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Registration submitted successfully!');
            registrationForm.reset();
        });
    </script>
</body>
</html>