import { driver } from "driver.js";
import "driver.js/dist/driver.css";

// --- Custom CSS for Wazzap.ai style ---
const style = document.createElement('style');
style.innerHTML = `
  .driver-popover.wazzap-popover {
    background-color: #ffffff;
    color: #1f2937;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    max-width: 350px;
    border: none;
  }
  .wazzap-popover .driver-popover-title {
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #111827;
    display: flex;
    align-items: center;
  }
  .wazzap-popover .driver-popover-description {
    font-size: 0.875rem;
    line-height: 1.5;
    color: #4b5563;
    margin-bottom: 20px;
  }
  .wazzap-popover .driver-popover-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 10px;
  }
  .wazzap-popover .driver-popover-progress-dots {
    display: flex;
    gap: 6px;
  }
  .wazzap-popover .progress-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #e5e7eb;
    transition: background-color 0.3s;
  }
  .wazzap-popover .progress-dot.active {
    background-color: #10b981;
    width: 8px;
    height: 8px;
  }
  .wazzap-popover .driver-popover-navigation {
    display: flex;
    gap: 8px;
  }
  .wazzap-popover button {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 6px 10px;
    cursor: pointer;
    transition: all 0.2s;
    color: #374151;
  }
  .wazzap-popover button:hover {
    background: #f3f4f6;
  }
  .wazzap-popover .driver-popover-next-btn {
    border-color: #10b981;
    color: #10b981;
  }
  @media (max-width: 640px) {
    .driver-popover.wazzap-popover {
      max-width: 90vw;
      padding: 16px;
    }
    .wazzap-popover .driver-popover-description {
      font-size: 0.8rem;
      margin-bottom: 12px;
    }
  }
`;
document.head.appendChild(style);

let autoAdvanceTimer = null;
const AUTO_ADVANCE_MS = 5000;

const resetTimer = () => {
  if (autoAdvanceTimer) clearTimeout(autoAdvanceTimer);
  autoAdvanceTimer = setTimeout(() => {
    if (driverObj.hasNextStep()) {
      driverObj.moveNext();
    } else {
      driverObj.destroy();
    }
  }, AUTO_ADVANCE_MS);
};

const steps = [
  {
    element: '#gest_config',
    popover: {
      title: '⚙️ Configuration',
      description: 'Terminer la configuration avant de passer a la gestion employé, paie , etats.',
      side: "right", align: 'start'
    }
  },
  {
    element: '#gest_emp',
    popover: {
      title: '👥 Gestion des Employés',
      description: 'Gérez vos collaborateurs, leurs dossiers, contrats et temps de travail.',
      side: "right", align: 'start'
    }
  },
  {
    element: '#gest_salary',
    popover: {
      title: '💰 Gestion de Paie',
      description: 'Calculez les salaires, gérez les retenues, les prêts et les primes.',
      side: "right", align: 'start'
    }
  },
  {
    element: '#gest_decla',
    popover: {
      title: '📄 Etats et Déclarations',
      description: 'Générez vos livres de paie et vos déclarations sociales en quelques clics.',
      side: "right", align: 'start'
    }
  }
];

const driverObj = driver({
  showProgress: false, // We'll use custom dots if needed, but for now standard simplified popover
  popoverClass: 'wazzap-popover',
  overlayColor: 'rgba(0,0,0,0.4)',
  steps: steps,
  onHighlightStarted: (element, step) => {
    resetTimer();
  },
  onDeselected: () => {
    if (autoAdvanceTimer) clearTimeout(autoAdvanceTimer);
  }
});

// Logic for first connection
const initGuide = () => {
  const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content') || 'anonymous';
  const guideKey = `rhflow_guide_v1_${userId}`;

  const hasSeenGuide = localStorage.getItem(guideKey);

  if (!hasSeenGuide) {
    let attempts = 0;
    const checkInterval = setInterval(() => {
      attempts++;
      const title = document.querySelector('#guide-dashboard-title');
      const menu = document.querySelector('#layout-menu');

      if (title || menu) {
        clearInterval(checkInterval);
        setTimeout(() => {
          driverObj.drive();
          localStorage.setItem(guideKey, 'true');
        }, 1500);
      } else if (attempts > 30) {
        clearInterval(checkInterval);
      }
    }, 500);
  }
};

window.startAiGuide = () => {
  driverObj.drive();
};

// Start logic when DOM is ready or immediately if already ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initGuide);
} else {
  initGuide();
}

console.log("AI Guide Script Loaded and Active!");
