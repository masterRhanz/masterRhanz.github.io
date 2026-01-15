<?php
session_start();

// Reset session for fresh start (optional - remove this line if you want to maintain state)
if (isset($_GET['reset']) || !isset($_SESSION['current_step'])) {
    session_destroy();
    session_start();
}

// Initialize session variables
if (!isset($_SESSION['name'])) {
    $_SESSION['name'] = '';
    $_SESSION['terminal_messages'] = [];
    $_SESSION['current_step'] = 'ask_name';
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'submit_name':
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $error = "Type your name muna 😜";
            } else {
                $_SESSION['name'] = $name;
                $_SESSION['terminal_messages'][] = "Hi $name! 😎";
                $_SESSION['current_step'] = 'ask_kamusta';
            }
            break;
            
        case 'kamusta_response_oks':
            $_SESSION['terminal_messages'][] = "Kilala ko crush mo! 😏";
            $_SESSION['current_step'] = 'ask_sino';
            break;

        case 'kamusta_response_hindi':
            $_SESSION['terminal_messages'][] = "Ay naku, hindi okay 😢";
            $_SESSION['current_step'] = 'comfort_questions';
            $_SESSION['comfort_index'] = 0;
            break;

        case 'sino_response':
            $_SESSION['terminal_messages'][] = "Ako crush mo eh 😍";
            $_SESSION['current_step'] = 'ask_oo_hindi';
            break;
            
         case 'oo_hindi_oo':
            $_SESSION['terminal_messages'][] = "Ihh crush din kita ❤️";
            $_SESSION['current_step'] = 'next_button';
            break;

        case 'oo_hindi_hindi':
            $_SESSION['terminal_messages'][] = "Ay naku 😢, okay lang...";
            $_SESSION['current_step'] = 'comfort_questions';
            $_SESSION['comfort_index'] = 0;
            break;
            
        case 'next_step':
            $_SESSION['current_step'] = 'ask_comment';
            break;
            
        case 'submit_comment':
            $comment = trim($_POST['comment'] ?? '');
            if (empty($comment)) {
                $error = "Type something muna 😅";
            } else {
                $_SESSION['terminal_messages'][] = "$_SESSION[name]: $comment";
                $_SESSION['current_step'] = 'final_message';
                $_SESSION['show_celebration'] = true;
            }
            break;
            
        case 'submit_comfort':
            $answer = trim($_POST['answer'] ?? '');
            if (empty($answer)) {
                $error = "Type muna 😅";
            } else {
                $_SESSION['terminal_messages'][] = "$_SESSION[name]: $answer";
                $_SESSION['comfort_index']++;
                if ($_SESSION['comfort_index'] >= 5) {
                    $_SESSION['terminal_messages'][] = "Sana okay ka na 😘";
                    $_SESSION['current_step'] = 'comfort_complete';
                    $_SESSION['show_healing'] = true;
                }
            }
            break;
            
        case 'restart':
            session_destroy();
            session_start();
            header('Location: index.php');
            exit;
    }
}

// Comfort questions
$comfort_questions = [
    "Ano ang isang bagay na ngayong araw ay nakapagpasaya sa'yo kahit konti? 🌸",
    "Kung puwede mong ulitin ang isang masayang sandali, alin yun at bakit? 🌅",
    "Anong simpleng bagay ang nakakapagpagaan ng pakiramdam mo kapag malungkot ka? 💖",
    "Ano ang huling bagay na nagpatawa sa'yo nang tawang-tawa ka? 😄",
    "Kung may isang mensahe para sa sarili mo ngayon, ano yun at paano ka nito mapapasaya? ✨"
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ultimate Confession</title>
    <style>
        body { 
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%); 
            color: #00ff88; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            text-align: center; 
            padding-top: 50px;
            min-height: 100vh;
        }
        
        #terminal { 
            width: 80%; 
            max-width: 600px;
            margin: 20px auto; 
            border: 1px solid #00ff88; 
            padding: 25px; 
            min-height: 150px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 255, 136, 0.1);
        }
        
        #terminal p {
            margin: 8px 0;
            font-size: 14px;
            line-height: 1.4;
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        #game { 
            width: 80%; 
            max-width: 600px;
            margin: 20px auto; 
            padding: 25px;
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid #00ff88;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 255, 136, 0.1);
        }
        
        #game p {
            margin: 15px 0;
            font-size: 16px;
            font-weight: 400;
        }
        
        input[type="text"] {
            width: 90%;
            max-width: 400px;
            padding: 12px 15px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid #00ff88;
            color: #00ff88;
            font-family: inherit;
            margin: 10px 0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        
        input[type="text"]:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 8px rgba(0, 255, 136, 0.2);
            background: rgba(0, 0, 0, 0.8);
        }
        
        input[type="text"]::placeholder {
            color: rgba(0, 255, 136, 0.5);
        }
        
        button { 
            padding: 12px 24px; 
            font-size: 14px; 
            cursor: pointer; 
            background: rgba(0, 255, 136, 0.1);
            color: #00ff88;
            border: 1px solid #00ff88;
            font-family: inherit;
            margin: 8px;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        button:hover {
            background: rgba(0, 255, 136, 0.2);
            transform: translateY(-1px);
            box-shadow: 0 2px 10px rgba(0, 255, 136, 0.2);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .error {
            color: #ff4444;
            margin: 10px 0;
        }
        
        #noBtn { 
            position: absolute; 
            transition: all 0.15s ease;
        }
        
        #popup { 
            display: none; 
            background: rgba(255, 0, 0, 0.9); 
            color: white; 
            padding: 20px; 
            position: fixed; 
            top: 30%; 
            left: 30%; 
            width: 40%; 
            z-index: 999;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(255, 0, 0, 0.3);
        }
        
        /* Simple particle effect */
        .particle {
            position: fixed;
            pointer-events: none;
            font-size: 20px;
            animation: floatUp 1s ease-out forwards;
            z-index: 1000;
        }
        
        @keyframes floatUp {
            0% {
                opacity: 1;
                transform: translateY(0) scale(0.5);
            }
            100% {
                opacity: 0;
                transform: translateY(-60px) scale(1);
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 20px 10px;
            }
            
            #terminal, #game {
                width: 95%;
                padding: 20px;
            }
            
            button {
                display: block;
                width: 90%;
                margin: 8px auto;
            }
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="?reset=1" style="color: #00ff88; text-decoration: none; font-size: 12px;">🔄 Start Fresh</a>
    </div>
    
    <div id="terminal">
        <?php foreach ($_SESSION['terminal_messages'] as $message): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endforeach; ?>
    </div>

    <div id="game">
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <?php if ($_SESSION['current_step'] === 'ask_name'): ?>
            <p>Hi! Ano pangalan mo? 😏</p>
            <form method="post">
                <input type="text" name="name" placeholder="Type your name" required>
                <br>
                <button type="submit" name="action" value="submit_name">Submit</button>
            </form>
            
        <?php elseif ($_SESSION['current_step'] === 'ask_kamusta'): ?>
            <p>Kamusta ka, <?= htmlspecialchars($_SESSION['name']) ?>? 😏</p>
            <form method="post">
              <button type="submit" name="action" value="kamusta_response_oks">
                  OKS lang
              </button>
              <button type="submit" name="action" value="kamusta_response_hindi">
                  Hindi okay
              </button>
            </form>

        <?php elseif ($_SESSION['current_step'] === 'ask_sino'): ?>
            <form method="post">
                <button type="submit" name="action" value="sino_response">Sino? 🤔</button>
            </form>
            
        <?php elseif ($_SESSION['current_step'] === 'ask_oo_hindi'): ?>
            <p>Oo o Hindi? 💖</p>
            <form method="post">
                <button type="submit" name="action" value="oo_hindi_oo">Oo 😘</button>
                <button type="submit" name="action" value="oo_hindi_hindi" id="noBtn">Hindi 😅</button>
            </form>
                
        <?php elseif ($_SESSION['current_step'] === 'next_button'): ?>
            <form method="post">
                <button type="submit" name="action" value="next_step">Next</button>
            </form>
            
        <?php elseif ($_SESSION['current_step'] === 'ask_comment'): ?>
            <p>Ano masasabi mo? 😏</p>
            <form method="post">
                <input type="text" name="comment" placeholder="Type here..." required>
                <br>
                <button type="submit" name="action" value="submit_comment">OK</button>
            </form>
            
        <?php elseif ($_SESSION['current_step'] === 'final_message'): ?>
            <p>Sorry na wag ka na magalit 😘 HAHAHAHAH gusto kasi kita <?= htmlspecialchars($_SESSION['name']) ?>...</p>
            <form method="post">
                <button type="submit" name="action" value="restart">Start Over</button>
            </form>
            <?php if (isset($_SESSION['show_celebration']) && $_SESSION['show_celebration']): ?>
                <script>
                    createCelebrationParticles();
                </script>
                <?php unset($_SESSION['show_celebration']); ?>
            <?php endif; ?>
            
        <?php elseif ($_SESSION['current_step'] === 'comfort_questions'): ?>
            <p><?= htmlspecialchars($comfort_questions[$_SESSION['comfort_index']]) ?></p>
            <form method="post">
                <input type="text" name="answer" placeholder="Type your answer..." required>
                <br>
                <button type="submit" name="action" value="submit_comfort">Send</button>
            </form>
            
        <?php elseif ($_SESSION['current_step'] === 'comfort_complete'): ?>
            <form method="post">
                <button type="submit" name="action" value="restart">Start Over</button>
            </form>
            <?php if (isset($_SESSION['show_healing']) && $_SESSION['show_healing']): ?>
                <script>
                    createHealingParticles();
                </script>
                <?php unset($_SESSION['show_healing']); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        // Enhanced particle effect system
        function createParticle(x, y, emoji = '✨') {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.innerHTML = emoji;
            particle.style.left = x + 'px';
            particle.style.top = y + 'px';
            
            document.body.appendChild(particle);
            
            setTimeout(() => {
                particle.remove();
            }, 1000);
        }

        // Add button effects
        function addButtonEffects() {
            document.querySelectorAll('button').forEach(button => {
                button.addEventListener('click', function(e) {
                    createParticle(e.clientX, e.clientY, '💚');
                });
            });
        }

        // Moving "Hindi" button
        const noBtn = document.getElementById('noBtn');
        if (noBtn) {
            noBtn.addEventListener('mouseover', function() {
                // Move much further away - up to 400px horizontally and 200px vertically
                const x = Math.floor(Math.random() * 800) - 400;
                const y = Math.floor(Math.random() * 400) - 200;
                this.style.position = 'relative';
                this.style.transition = 'all 0.1s ease';
                this.style.transform = `translate(${x}px, ${y}px)`;
                
                // Create laughing particle
                createParticle(
                    this.getBoundingClientRect().left + this.offsetWidth / 2,
                    this.getBoundingClientRect().top + this.offsetHeight / 2,
                    '😄'
                );
                
                // Add taunting message
                setTimeout(() => {
                    createParticle(
                        window.innerWidth / 2,
                        window.innerHeight / 2,
                        '😜 Hindi mo ako maa-click!'
                    );
                }, 100);
            });
            
            // Also move on mouseenter for immediate response
            noBtn.addEventListener('mouseenter', function() {
                const x = Math.floor(Math.random() * 600) - 300;
                const y = Math.floor(Math.random() * 300) - 150;
                this.style.position = 'relative';
                this.style.transition = 'all 0.05s ease';
                this.style.transform = `translate(${x}px, ${y}px)`;
            });
        }

        // Auto-focus inputs
        const inputs = document.querySelectorAll('input[type="text"]');
        if (inputs.length > 0) {
            inputs[inputs.length - 1].focus();
        }

        // Add enter key support for all text inputs
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const form = this.closest('form');
                    if (form) {
                        const submitBtn = form.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.click();
                        }
                    }
                }
            });
        });

        // Enhanced celebration particles for successful confession
        function createCelebrationParticles() {
            for (let i = 0; i < 3; i++) {
                setTimeout(() => {
                    createParticle(
                        window.innerWidth / 2 + (Math.random() - 0.5) * 100,
                        window.innerHeight / 2,
                        ['💖', '✨', '💕'][Math.floor(Math.random() * 3)]
                    );
                }, i * 200);
            }
        }

        // Healing particles for comfort flow
        function createHealingParticles() {
            for (let j = 0; j < 5; j++) {
                setTimeout(() => {
                    createParticle(
                        Math.random() * window.innerWidth,
                        Math.random() * window.innerHeight,
                        ['💖', '🌸', '✨'][Math.floor(Math.random() * 3)]
                    );
                }, j * 200);
            }
        }
    </script>
</body>
</html>
