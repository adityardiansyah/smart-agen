import { cva, type VariantProps } from 'class-variance-authority';

export { default as Button } from './Button.vue';

export const buttonVariants = cva(
    'inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0',
    {
        variants: {
            variant: {
                default: 'bg-brand-primary text-text-on-color shadow-smart-card hover:bg-brand-primary/90',
                destructive: 'bg-destructive text-destructive-foreground shadow-smart-card hover:bg-destructive/90',
                outline: 'border border-input bg-bg-card shadow-smart-card hover:bg-brand-secondary/20 text-text-primary',
                secondary: 'bg-brand-secondary text-text-on-color shadow-smart-card hover:bg-brand-secondary/80',
                ghost: 'text-text-primary hover:bg-brand-secondary/20',
                link: 'text-brand-primary underline-offset-4 hover:underline',
            },
            size: {
                default: 'h-9 px-4 py-2 rounded-smart-md',
                sm: 'h-8 rounded-smart-sm px-3 text-xs',
                lg: 'h-10 rounded-smart-lg px-8',
                icon: 'h-9 w-9 rounded-smart-md',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    },
);

export type ButtonVariants = VariantProps<typeof buttonVariants>;
