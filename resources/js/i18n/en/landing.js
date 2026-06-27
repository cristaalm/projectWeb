export default {
  header: {
    menu: {
      howItWorks: 'How it works',
      benefits: 'Benefits',
      impact: 'Impact',
      technical: 'Technical',
    },

    actions: {
      goToPanel: 'Go to dashboard',
      downloadApp: 'Download App',
    },

    dialog: {
      downloadTitle: 'Download EcoSort App',
      downloadText: 'Do you want to download the app? If you continue, an APK file will be downloaded.',
      confirm: 'Download',
      cancel: 'Cancel',
    },
  },

  hero: {
    chip: 'Earn rewards by recycling',

    title: {
      main: 'Recycle and earn',
      highlight: 'rewards',
    },

    description:
      'Turn your plastic bottles and aluminum cans into redeemable points. EcoSort makes recycling easy, fun, and rewarding.',

    actions: {
      download: 'Download App',
      howItWorks: 'See how it works',
    },

    stats: {
      users: {
        value: '+1000',
        label: 'Active users',
      },
      containers: {
        value: '24/7',
        label: 'Available containers',
      },
    },

    imageAlt: 'EcoSort smart container',
  },

  howItWorks: {
    title: 'So easy and fast, in 3 steps',
    subtitle:
      'Recycling has never been this simple. All you need is your phone and the desire to make a difference.',

    steps: [
      {
        icon: 'mdi mdi-qrcode',
        title: 'Scan your code',
        description:
          'Open the EcoSort app and show your unique QR code at the smart container.',
      },
      {
        icon: 'mdi mdi-camera',
        title: 'Deposit your materials',
        description:
          'Place your plastic bottles or aluminum cans. Our smart container will identify them automatically.',
      },
      {
        icon: 'mdi mdi-gift',
        title: 'Earn points',
        description:
          'Receive points instantly and redeem them for products, discounts, and benefits at partner stores.',
      },
    ],

    videoFallback: 'Your browser does not support videos.',
  },

  benefits: {
    title: 'Why choose EcoSort?',
    subtitle:
      'More than a recycling app, it is a complete ecosystem that rewards you for doing the right thing.',

    videoFallback: 'Your browser does not support videos.',

    items: [
      {
        icon: 'mdi mdi-cash-multiple',
        title: 'Earn while caring for the planet',
        description:
          'Every bottle or can you recycle turns into real points you can use at your favorite stores.',
      },
      {
        icon: 'mdi mdi-trending-up',
        title: 'Gamified rewards system',
        description:
          'Unlock achievements, level up, and access exclusive rewards the more you recycle.',
      },
      {
        icon: 'mdi mdi-account-group',
        title: 'Support local businesses',
        description:
          'Redeem your points at partner businesses in your community and strengthen the local economy.',
      },
      {
        icon: 'mdi mdi-creation',
        title: 'Smart technology',
        description:
          'Our AI automatically identifies materials. No hassle, no mistakes.',
      },
    ],
  },

  impact: {
    title: 'Your impact matters',

    paragraphs: [
      'Mexico recycles only 9.6% of its waste, far below the OECD average of 20%. With EcoSort, we are changing this reality, one bottle at a time.',
      'Our system directly contributes to the United Nations Sustainable Development Goals, specifically SDG 11 (Sustainable Cities) and SDG 12 (Responsible Consumption and Production).',
    ],

    stats: [
      {
        value: '9.6%',
        label: 'Current recycling rate in Mexico',
      },
      {
        value: '63%',
        label: 'PET recovery rate in Mexico (continental leader)',
      },
      {
        value: '82.5%',
        label: 'Mexicans aware of the problem',
      },
      {
        value: '51.3%',
        label: 'Effectively separate waste',
      },
    ],
  },

  technical: {
    title: 'Cutting-edge technology',
    subtitle:
      'A complete ecosystem integrating hardware, software, and cloud services.',

    images: {
      scannerAlt: 'QR Scanner',
      cameraAlt: 'Raspberry Pi Camera',
      raspberryAlt: 'Raspberry Pi 4',
      diagramAlt: 'System diagram',
    },

    table: {
      hardwareTitle: 'Hardware description',
      productTitle: 'Smart Container',

      hardware: [
        { label: 'Material', value: 'Melamine' },
        { label: 'Camera', value: 'Full HD NoIR V2' },
        { label: 'Display', value: '16x2 LCD Display' },
        { label: 'Scanner', value: 'Generic 1D & 2D / QR' },
      ],

      softwareTitle: 'Software description',

      software: [
        { label: 'AI', value: 'GPT-4o for material classification' },
        { label: 'Server', value: 'Render' },
        { label: 'Database', value: 'PostgreSQL' },
        {
          label: 'Languages & Frameworks',
          value: 'Laravel, Vue.js, Tailwind CSS, Kotlin, JavaScript, PHP',
        },
      ],
    },
  },

  cta: {
    dialog: {
      title: 'Download EcoSort App',
      text: 'Do you want to download the app? If you continue, an APK file will be downloaded.',
      confirm: 'Download',
      cancel: 'Cancel',
    },

    imageAlt: 'EcoSort app on mobile device',

    title: 'Start recycling today',
    subtitle:
      'Join thousands of people already earning rewards while taking care of the planet.',

    buttons: {
      download: 'Download App for Free',
      login: 'I am EcoSort',
    },
  },

  footer: {
    description:
      'Transforming recycling in Mexico through smart technology and rewards.',

    product: {
      title: 'Product',
      howItWorks: 'How It Works',
      benefits: 'Benefits',
      impact: 'Impact',
      technology: 'Technology',
    },

    social: {
      title: 'Social Media',
      facebook: 'Facebook',
      instagram: 'Instagram',
    },

    contact: {
      title: 'Contact',
      location: 'Manzanillo, Colima, Mexico.',
    },

    legal: {
      rights: 'All rights reserved.',
      terms: 'Terms and conditions',
    },
  },
}
