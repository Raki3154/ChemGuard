<?php 
session_start(); 
if(!isset($_SESSION['plant_name'])) 
{ 
    header("Location: login.php"); 
    exit(); 
} 
date_default_timezone_set("Asia/Kolkata"); 

$executive_board = [
    [
        'name' => 'Dr. Rajesh Kapoor',
        'designation' => 'Former UN SDG Advisor',
        'credentials' => 'PhD Sustainable Engineering, 20+ years UN advisory',
        'specialization' => 'SDG Integration & Policy Framework',
        'avatar' => '👨‍🎓',
        'current_role' => 'Senior Partner, Global Sustainability Consultancy'
    ],
    [
        'name' => 'Ms. Priya Sharma',
        'designation' => 'Ex-CEO, GreenTech Industries',
        'credentials' => 'MBA Harvard, Forbes 30 Under 30 Sustainability',
        'specialization' => 'Sustainable Business Transformation',
        'avatar' => '👩‍💼',
        'current_role' => 'Board Member, Multiple ESG-focused Companies'
    ],
];

$sdg_solutions = [
    '7' => [
        'title' => 'Affordable & Clean Energy',
        'icon' => '⚡',
        'common_problems' => [
            'High energy consumption in boiler operations',
            'Reliance on non-renewable energy sources',
            'Inefficient heat transfer systems',
            'High carbon emissions from energy use'
        ],
        'solutions' => [
            'Implement solar thermal systems for pre-heating boiler feedwater',
            'Upgrade to high-efficiency burners and heat exchangers',
            'Install waste heat recovery systems',
            'Transition to biomass or biogas as alternative fuel sources',
            'Implement smart energy monitoring and control systems'
        ],
        'efficiency_metrics' => ['Energy Efficiency', 'Renewable %', 'Carbon Intensity'],
        'implementation_timeline' => '6-18 months',
        'investment_level' => 'Medium to High',
        'confidence_score' => 85
    ],
    '9' => [
        'title' => 'Industry, Innovation & Infrastructure',
        'icon' => '🏭',
        'common_problems' => [
            'Outdated monitoring and control systems',
            'Lack of predictive maintenance capabilities',
            'Inefficient production processes',
            'Poor infrastructure resilience'
        ],
        'solutions' => [
            'Implement Industry 4.0 IoT sensors for real-time monitoring',
            'Develop AI-powered predictive maintenance algorithms',
            'Upgrade to automated control systems',
            'Implement digital twin technology for process optimization',
            'Establish innovation lab for continuous improvement'
        ],
        'efficiency_metrics' => ['Digital Maturity', 'Innovation Index', 'Infrastructure Resilience'],
        'implementation_timeline' => '12-24 months',
        'investment_level' => 'High',
        'confidence_score' => 82
    ],
    '12' => [
        'title' => 'Responsible Consumption & Production',
        'icon' => '🔄',
        'common_problems' => [
            'High water consumption in operations',
            'Chemical waste and byproduct generation',
            'Inefficient resource utilization',
            'Poor waste management practices'
        ],
        'solutions' => [
            'Implement closed-loop water recycling systems',
            'Optimize chemical dosing through AI algorithms',
            'Develop circular economy partnerships for waste utilization',
            'Implement lean manufacturing principles',
            'Establish supplier sustainability assessment program'
        ],
        'efficiency_metrics' => ['Waste Reduction', 'Recycling Rate', 'Supply Chain Sustainability'],
        'implementation_timeline' => '3-12 months',
        'investment_level' => 'Medium',
        'confidence_score' => 88
    ]
];

$date = date("d-m-Y"); 
$time = date("H:i:s");
$analysis_results = [];
$selected_sdgs = [];
$current_challenge = '';
$overall_confidence = 0;

if(isset($_POST['analyze']))
{ 
    $selected_sdgs = $_POST['sdg'] ?? [];
    $current_challenge = $_POST['current_challenge'] ?? '';
    
    // Only process selected SDGs
    $analysis_results = [];
    $total_confidence = 0;
    $processed_sdgs = 0;
    
    foreach($selected_sdgs as $sdg) {
        if(isset($sdg_solutions[$sdg])) {
            $solution = $sdg_solutions[$sdg];
            $analysis_results[] = [
                'sdg' => $sdg,
                'title' => $solution['title'],
                'icon' => $solution['icon'],
                'common_problems' => $solution['common_problems'],
                'solutions' => $solution['solutions'],
                'efficiency_metrics' => $solution['efficiency_metrics'],
                'implementation_timeline' => $solution['implementation_timeline'],
                'investment_level' => $solution['investment_level'],
                'confidence_score' => $solution['confidence_score']
            ];
            $total_confidence += $solution['confidence_score'];
            $processed_sdgs++;
        }
    }
    
    // Calculate overall confidence only for processed SDGs
    $overall_confidence = $processed_sdgs > 0 ? $total_confidence / $processed_sdgs : 0;
    
    $_SESSION['analysis_data'] = [
        'results' => $analysis_results,
        'confidence' => $overall_confidence,
        'challenge' => $current_challenge,
        'selected_sdgs' => $selected_sdgs
    ];
    
    header("Location: ".$_SERVER['PHP_SELF']."#resultsPanel"); 
    exit(); 
}

if(isset($_SESSION['analysis_data'])) 
{
    $analysis_results = $_SESSION['analysis_data']['results'];
    $overall_confidence = $_SESSION['analysis_data']['confidence'];
    $current_challenge = $_SESSION['analysis_data']['challenge'];
    $selected_sdgs = $_SESSION['analysis_data']['selected_sdgs'];
    unset($_SESSION['analysis_data']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ChemGuard - Executive Mentoring</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/mentoring.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-left">
                <h1>ChemGuard Executive Mentoring</h1>
                <div class="plant-info">Plant: <?php echo $_SESSION['plant_name']; ?></div>
            </div>
            <div class="nav-links">
                <a href="home.php">Home</a>
                <a href="about.php">About</a>
                <a href="logout.php">Logout</a>
            </div>
        </header>

        <div class="main-content">
            <div class="mentoring-panel">
                <h2 class="panel-title">Executive Advisory Board</h2>
                <div class="executive-grid">
                    <?php foreach($executive_board as $executive): ?>
                    <div class="executive-card">
                        <div class="executive-header">
                            <div class="executive-avatar"><?php echo $executive['avatar']; ?></div>
                            <div class="executive-info">
                                <h3><?php echo $executive['name']; ?></h3>
                                <div class="executive-role"><?php echo $executive['designation']; ?></div>
                            </div>
                        </div>
                        <div class="executive-credentials"><?php echo $executive['credentials']; ?></div>
                        <div class="executive-specialization"><?php echo $executive['specialization']; ?></div>
                        <div style="margin-top: 0.8rem; font-size: 0.8rem; color: #888;">
                            <?php echo $executive['current_role']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="analysis-interface">
                <form method="POST" id="analysisForm">
                    <h2 class="panel-title">SDG Problem-Solution Analysis</h2>
                    
                    <div class="sdg-selector">
                        <h4>Select SDG Focus Areas for Solution Analysis</h4>
                        <div class="sdg-options-grid">
                            <?php foreach($sdg_solutions as $sdg => $data): ?>
                            <div class="sdg-option <?php echo in_array($sdg, $selected_sdgs) ? 'selected' : ''; ?>" 
                                 onclick="toggleSDG('<?php echo $sdg; ?>', this)">
                                <div class="sdg-icon"><?php echo $data['icon']; ?></div>
                                <div class="sdg-title">SDG <?php echo $sdg; ?></div>
                                <div class="sdg-desc"><?php echo $data['title']; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- hidden inputs container -->
                        <div id="sdgHiddenInputs">
                            <?php foreach($selected_sdgs as $sdg): ?>
                                <input type="hidden" name="sdg[]" id="sdg<?php echo $sdg; ?>" value="<?php echo $sdg; ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="context-input">
                        <h4>Describe Your Operational Problem</h4>
                        <textarea name="current_challenge" placeholder="What specific problem are you facing?"><?php echo htmlspecialchars($current_challenge); ?></textarea>
                    </div>

                    <button type="submit" class="analyze-btn" name="analyze">
                        <i class="fas fa-brain"></i> Generate Solutions
                    </button>
                </form>

                <?php if(!empty($analysis_results)): ?>
<div class="results-panel" id="resultsPanel">
    <div class="confidence-header">
        <div>
            <h3 style="color: var(--accent); margin-bottom: 0.5rem;">Executive Solution Analysis</h3>
            <p style="color: #b0b3b8;">
                Solutions for 
                <?php 
                $sdg_names = [];
                foreach($analysis_results as $result) {
                    $sdg_names[] = "SDG " . $result['sdg'];
                }
                echo implode(', ', $sdg_names);
                ?>
            </p>
        </div>
        <div class="confidence-score">
            <div class="score-circle">
                <div class="score-value"><?php echo round($overall_confidence); ?>%</div>
            </div>
            <div class="score-label">Solution Confidence</div>
        </div>
    </div>

    <div class="analysis-results">
        <?php foreach($analysis_results as $analysis): ?>
        <div class="sdg-analysis-card">
            <div class="sdg-analysis-header">
                <div class="sdg-analysis-title">
                    <span style="font-size: 1.5rem; margin-right: 0.5rem;"><?php echo $analysis['icon']; ?></span>
                    SDG <?php echo $analysis['sdg']; ?>: <?php echo $analysis['title']; ?>
                </div>
                <div class="sdg-analysis-score"><?php echo $analysis['confidence_score']; ?>%</div>
            </div>
            
            <div class="problems-section">
                <h4 class="section-title">Common Problems Addressed</h4>
                <?php foreach($analysis['common_problems'] as $problem): ?>
                <div class="problem-item">
                    <i class="fas fa-exclamation-triangle" style="color: #ff9800; margin-right: 0.5rem;"></i>
                    <?php echo $problem; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="solutions-section">
                <h4 class="section-title">Executive-Recommended Solutions</h4>
                <?php foreach($analysis['solutions'] as $solution): ?>
                <div class="solution-item">
                    <i class="fas fa-check-circle" style="color: var(--success); margin-right: 0.5rem;"></i>
                    <?php echo $solution; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="metrics-timeline">
                <div class="metric-box">
                    <div class="metric-label">Efficiency Metrics</div>
                    <div class="metric-value"><?php echo implode(', ', $analysis['efficiency_metrics']); ?></div>
                </div>
                <div class="metric-box">
                    <div class="metric-label">Implementation Timeline</div>
                    <div class="metric-value"><?php echo $analysis['implementation_timeline']; ?></div>
                </div>
                <div class="metric-box">
                    <div class="metric-label">Investment Level</div>
                    <div class="metric-value"><?php echo $analysis['investment_level']; ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
            </div>
        </div>
    </div>

    <div class="chatbot-trigger" id="chatbotTrigger">
        <i class="fas fa-robot"></i>
    </div>

    <script>
function toggleSDG(sdg, element) {
    const hiddenInputsDiv = document.getElementById('sdgHiddenInputs');
    const existingInput = document.getElementById('sdg' + sdg);

    if (element.classList.contains('selected')) {
        // Unselect
        element.classList.remove('selected');
        if (existingInput) existingInput.remove();
    } else {
        // Select
        element.classList.add('selected');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'sdg[]';
        input.value = sdg;
        input.id = 'sdg' + sdg;
        hiddenInputsDiv.appendChild(input);
    }

    updateSelectedSDGsDisplay();
}

function updateSelectedSDGsDisplay() {
    const selectedCount = document.querySelectorAll('.sdg-option.selected').length;
    const analyzeBtn = document.querySelector('.analyze-btn');
    
    if (selectedCount > 0) {
        analyzeBtn.innerHTML = `<i class="fas fa-brain"></i> Generate Solutions for ${selectedCount} SDG${selectedCount > 1 ? 's' : ''}`;
        analyzeBtn.disabled = false;
        analyzeBtn.style.opacity = '1';
    } else {
        analyzeBtn.innerHTML = `<i class="fas fa-brain"></i> Select SDGs to Generate Solutions`;
        analyzeBtn.disabled = true;
        analyzeBtn.style.opacity = '0.6';
    }
}

// Auto-resize textareas
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});

// Chatbot trigger
document.getElementById('chatbotTrigger').addEventListener('click', function() {
    window.location.href = 'ai.php';
});

// Initialize state
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedSDGsDisplay();
    <?php if(!empty($analysis_results)): ?>
    setTimeout(() => {
        const resultsPanel = document.getElementById('resultsPanel');
        if (resultsPanel) {
            resultsPanel.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }
    }, 500);
    <?php endif; ?>
});
    </script>
</body>
</html>
