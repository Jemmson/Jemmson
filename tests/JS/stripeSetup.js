// https://github.com/stripe/react-stripe-elements/issues/427

// Mocking Stripe object
const elementMock = {
    mount: jest.fn(),
    destroy: jest.fn(),
    on: jest.fn(),
    update: jest.fn(),
    addEventListener: jest.fn(),
};

const elementsMock = {
    create: jest.fn().mockReturnValue(elementMock),
};

const stripeMock = {
    elements: jest.fn().mockReturnValue(elementsMock),
    createToken: jest.fn(() => Promise.resolve()),
    createSource: jest.fn(() => Promise.resolve()),
};

// Set the global Stripe
global.Stripe = jest.fn().mockReturnValue(stripeMock);