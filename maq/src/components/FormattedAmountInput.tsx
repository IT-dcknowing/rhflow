import React from 'react';

/**
 * Helper to format a number into spaced thousands string (e.g. 450000 -> "450 000")
 */
export function formatThousands(val: number | string | null | undefined): string {
  if (val === null || val === undefined || val === '') return '';
  const num = typeof val === 'number' ? val : parseInt(String(val).replace(/\D/g, ''), 10);
  if (isNaN(num)) return '';
  return num.toLocaleString('fr-FR');
}

/**
 * Parses a string with spaces or other non-digits into a numeric integer
 */
export function parseThousands(str: string): number {
  const clean = str.replace(/\D/g, '');
  if (!clean) return 0;
  return parseInt(clean, 10) || 0;
}

interface FormattedAmountInputProps extends Omit<React.InputHTMLAttributes<HTMLInputElement>, 'value' | 'onChange'> {
  value: number;
  onChange: (val: number) => void;
  currencySuffix?: string;
  containerClassName?: string;
}

/**
 * High-visibility formatted amount input.
 * Displays formatted thousands (e.g. "450 000") using Montserrat font,
 * while safely maintaining and returning the integer numeric value.
 */
export const FormattedAmountInput: React.FC<FormattedAmountInputProps> = ({
  value,
  onChange,
  currencySuffix,
  className = '',
  containerClassName = '',
  disabled,
  placeholder = '0',
  ...rest
}) => {
  // Local display string state so the user can backspace or type freely
  const [displayValue, setDisplayValue] = React.useState<string>(() => (value ? value.toLocaleString('fr-FR') : ''));

  // Sync display string whenever external value changes
  React.useEffect(() => {
    setDisplayValue(value ? value.toLocaleString('fr-FR') : '');
  }, [value]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const raw = e.target.value;
    const numeric = parseThousands(raw);
    setDisplayValue(raw ? numeric.toLocaleString('fr-FR') : '');
    onChange(numeric);
  };

  const handleBlur = () => {
    setDisplayValue(value ? value.toLocaleString('fr-FR') : '');
  };

  return (
    <div className={`relative inline-flex items-center ${containerClassName}`}>
      <input
        type="text"
        inputMode="numeric"
        disabled={disabled}
        placeholder={placeholder}
        value={displayValue}
        onChange={handleChange}
        onBlur={handleBlur}
        className={`font-montserrat font-semibold tracking-tight text-right ${className}`}
        {...rest}
      />
      {currencySuffix && (
        <span className="text-xs font-montserrat font-medium text-[#6B6B6B] ml-1.5 select-none pointer-events-none">
          {currencySuffix}
        </span>
      )}
    </div>
  );
};
