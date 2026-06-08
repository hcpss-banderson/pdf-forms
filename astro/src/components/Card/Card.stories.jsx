import Card from './Card.astro';
import react from '@storybook/react';

export default {
  title: 'Components/Card',
  component: Card,
};

export const Default = {
    args: {
        slots: {
            default: <p>This is a card</p>,
        },
    },
};
